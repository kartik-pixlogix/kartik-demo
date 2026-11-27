/*
 * Turns the raw voiceovers (audio/vo/NN.mp3, one take per reel with a pause
 * between beats) into what the reels need:
 *
 *   audio/mix/NN.m4a   voice (tightened, slightly faster) + ducked music + transition sounds
 *   src/timing.js      window.TIMING: for each reel its length, when each beat
 *                      starts, and when each caption shows
 *
 * The voice drives the picture: the five beats are found from the pauses in
 * the recording, then each is re-timed with a fixed short gap, so the scene
 * cuts land exactly where she starts a new thought. A reel with no voiceover
 * file gets estimated timings and music only.
 *
 *   node social/instagram/src/prep-audio.mjs
 */
import { execFileSync, spawnSync } from 'node:child_process';
import { existsSync, mkdirSync, writeFileSync } from 'node:fs';
import { dirname, join } from 'node:path';
import { fileURLToPath } from 'node:url';
import { reels, say } from './reels.mjs';

const HERE = dirname(fileURLToPath(import.meta.url));
const AUDIO = join(HERE, '..', 'audio');
const MIX = join(AUDIO, 'mix');
const TMP = join(AUDIO, 'tmp');
mkdirSync(MIX, { recursive: true });
mkdirSync(TMP, { recursive: true });

const TEMPO = 1.14;   // a little brisker than the take, pitch kept
const LEAD = 0.45;    // silence before the first word, so the hook can land
const GAP = 0.3;      // between beats
const TAIL = 1.7;     // held on the logo after the last word
const CPS = 15.5;     // characters per second, for reels with no voice

const pad = (n) => String(n).padStart(2, '0');
const ff = (args) => execFileSync('ffmpeg', ['-y', '-loglevel', 'error', ...args]);
const duration = (f) => Number(execFileSync('ffprobe', ['-v', 'error', '-show_entries', 'format=duration', '-of', 'csv=p=0', f]).toString());

function silences(file) {
    const out = spawnSync('ffmpeg', ['-hide_banner', '-i', file, '-af', 'silencedetect=noise=-38dB:d=0.16', '-f', 'null', '-']).stderr.toString();
    const starts = [...out.matchAll(/silence_start: ([\d.]+)/g)].map((m) => Number(m[1]));
    const ends = [...out.matchAll(/silence_end: ([\d.]+)/g)].map((m) => Number(m[1]));
    return starts.map((s, i) => ({ start: s, end: ends[i] ?? s, dur: (ends[i] ?? s) - s }));
}

/** Caption pieces: short phrases, broken at punctuation where possible. */
function chunks(sentence) {
    const words = sentence.split(' ');
    const out = [];
    let cur = '';
    for (const w of words) {
        const next = cur ? `${cur} ${w}` : w;
        if (cur && (next.length > 26 || /[,:]$/.test(cur) && cur.length > 12)) { out.push(cur); cur = w; } else cur = next;
    }
    if (cur) out.push(cur);
    // no one-word orphan at the end ("them.")
    if (out.length > 1 && out[out.length - 1].length < 9 && out[out.length - 2].length + out[out.length - 1].length < 34) {
        const last = out.pop();
        out[out.length - 1] += ' ' + last;
    }
    return out;
}
/** Sentences, keeping the web address whole. */
const sentencesOf = (line) => line.replace('ubshield.in/contact', 'ubshield~in/contact')
    .match(/[^.?!]+[.?!]*\s*/g).map((s) => s.trim().replace('ubshield~in', 'ubshield.in')).filter(Boolean);

function captionsFor(line, a, b, breaks) {
    const sents = sentencesOf(line);
    // sentence windows: from real pauses when they match, else by length
    let edges;
    if (breaks.length === sents.length - 1) edges = [a, ...breaks, b];
    else {
        const total = sents.reduce((n, s) => n + s.length, 0);
        let acc = 0;
        edges = [a, ...sents.slice(0, -1).map((s) => { acc += s.length; return a + ((b - a) * acc) / total; }), b];
    }
    const caps = [];
    sents.forEach((s, i) => {
        const parts = chunks(s);
        const total = parts.reduce((n, p) => n + p.length, 0);
        let t = edges[i];
        for (const p of parts) {
            const d = ((edges[i + 1] - edges[i]) * p.length) / total;
            caps.push({ a: +t.toFixed(3), b: +(t + d).toFixed(3), text: p });
            t += d;
        }
    });
    return caps;
}

// transition sounds, synthesized once
const WHOOSH = join(TMP, 'whoosh.wav');
const HIT = join(TMP, 'hit.wav');
ff(['-f', 'lavfi', '-i', 'anoisesrc=d=0.55:c=pink:r=44100:a=0.6', '-af', 'highpass=f=500,lowpass=f=6000,afade=t=in:d=0.26:curve=qsin,afade=t=out:st=0.26:d=0.29,volume=0.5', '-ac', '2', WHOOSH]);
ff(['-f', 'lavfi', '-i', 'sine=frequency=58:duration=0.4:sample_rate=44100', '-af', 'afade=t=in:d=0.004,afade=t=out:st=0.03:d=0.36:curve=exp,volume=1.3', '-ac', '2', HIT]);

const timing = {};

for (const id of Object.keys(reels).map(Number)) {
    const lines = reels[id].vo;
    const raw = join(AUDIO, 'vo', `${pad(id)}.mp3`);
    const hasVoice = existsSync(raw);
    const voice = []; // [start, end] of each beat's speech on the final timeline
    let caps = [];

    if (hasVoice) {
        const sil = silences(raw);
        const total = duration(raw);
        // the four beat breaks: the pause nearest to where each [pause] should fall
        const weights = say(id).split(' [pause] ').map((s) => s.length + 14);
        const sum = weights.reduce((a, b) => a + b, 0);
        let acc = 0;
        const expected = weights.slice(0, -1).map((w) => { acc += w; return (acc / sum) * total; });
        const cands = sil.filter((s) => s.dur >= 0.38 && s.start > 0.5 && s.end < total - 0.4);
        const picks = [];
        for (const e of expected) {
            const pool = cands.filter((c) => !picks.includes(c) && (!picks.length || c.start > picks[picks.length - 1].end));
            if (!pool.length) throw new Error(`reel ${id}: could not find the beat pauses`);
            picks.push(pool.reduce((best, c) => (Math.abs((c.start + c.end) / 2 - e) < Math.abs((best.start + best.end) / 2 - e) ? c : best)));
        }
        const head = sil.find((s) => s.start < 0.05)?.end ?? 0;
        const tailSil = sil.find((s) => s.end > total - 0.05);
        const rawBeats = lines.map((_, k) => [k === 0 ? head : picks[k - 1].end - 0.03, k === lines.length - 1 ? (tailSil ? tailSil.start : total) : picks[k].start + 0.06]);

        let t = LEAD;
        const parts = [];
        rawBeats.forEach(([a, b], k) => {
            const seg = join(TMP, `${pad(id)}-${k}.wav`);
            ff(['-i', raw, '-af', `atrim=${a}:${b},asetpts=PTS-STARTPTS,atempo=${TEMPO},afade=t=in:d=0.012,afade=t=out:st=${Math.max(0, (b - a) / TEMPO - 0.05)}:d=0.05`, '-ar', '44100', '-ac', '2', seg]);
            const d = duration(seg);
            voice.push([t, t + d]);
            parts.push({ seg, at: t });
            // sentence breaks inside the beat, from its own pauses
            const inner = sil.filter((s) => s.dur >= 0.24 && s.start > a + 0.3 && s.end < b - 0.3).map((s) => t + ((s.start + s.end) / 2 - a) / TEMPO);
            caps = caps.concat(captionsFor(lines[k], t, t + d, inner));
            t += d + GAP;
        });
        const dur = +(t - GAP + TAIL).toFixed(3);
        const b = voice.map(([s], k) => (k === 0 ? 0 : +(s - 0.18).toFixed(3)));

        // voice bus
        const vbus = join(TMP, `${pad(id)}-voice.wav`);
        const vin = parts.flatMap((p) => ['-i', p.seg]);
        const vfl = parts.map((p, i) => `[${i}:a]adelay=${Math.round(p.at * 1000)}:all=1[v${i}]`).join(';') + ';' + parts.map((_, i) => `[v${i}]`).join('') + `amix=inputs=${parts.length}:normalize=0,apad=whole_dur=${dur},loudnorm=I=-15:TP=-1.5:LRA=9[out]`;
        ff([...vin, '-filter_complex', vfl, '-map', '[out]', '-ar', '44100', '-ac', '2', '-t', String(dur), vbus]);
        timing[id] = { dur, b, caps, voice: true };
        mix(id, dur, b, vbus);
    } else {
        let t = LEAD;
        lines.forEach((line) => {
            const d = line.length / CPS;
            voice.push([t, t + d]);
            caps = caps.concat(captionsFor(line, t, t + d, []));
            t += d + GAP;
        });
        const dur = +(t - GAP + TAIL).toFixed(3);
        const b = voice.map(([s], k) => (k === 0 ? 0 : +(s - 0.18).toFixed(3)));
        timing[id] = { dur, b, caps, voice: false };
        mix(id, dur, b, null);
    }
    console.log(`${pad(id)}  ${timing[id].dur.toFixed(1)}s  beats ${timing[id].b.map((x) => x.toFixed(1)).join(' ')}  ${hasVoice ? 'voice' : 'NO VOICE (music only)'}`);
}

/** Music ducked under the voice, a whoosh on every scene change, a soft hit on the hook. */
function mix(id, dur, b, vbus) {
    const out = join(MIX, `${pad(id)}.m4a`);
    const music = join(AUDIO, 'music.mp3');
    const inputs = ['-stream_loop', '-1', '-i', music, '-i', WHOOSH, '-i', HIT];
    if (vbus) inputs.push('-i', vbus);
    const whooshAt = b.slice(1).map((t) => Math.max(0, t - 0.2));
    const fl = [
        `[0:a]atrim=0:${dur},asetpts=PTS-STARTPTS,volume=${vbus ? 0.42 : 0.7},afade=t=in:d=0.25,afade=t=out:st=${dur - 1.3}:d=1.3[m0]`,
        `[1:a]asplit=${whooshAt.length}${whooshAt.map((_, i) => `[w${i}]`).join('')}`,
        ...whooshAt.map((t, i) => `[w${i}]adelay=${Math.round(t * 1000)}:all=1[wd${i}]`),
        `[2:a]adelay=${Math.round(0.22 * 1000)}:all=1,volume=0.7[h0]`,
    ];
    if (vbus) {
        fl.push(`[3:a]asplit=2[vk][vo]`, `[m0][vk]sidechaincompress=threshold=0.03:ratio=8:attack=30:release=450[md]`);
        fl.push(`[md][vo]${whooshAt.map((_, i) => `[wd${i}]`).join('')}[h0]amix=inputs=${3 + whooshAt.length}:normalize=0,alimiter=limit=0.95[out]`);
    } else {
        fl.push(`[m0]${whooshAt.map((_, i) => `[wd${i}]`).join('')}[h0]amix=inputs=${2 + whooshAt.length}:normalize=0,alimiter=limit=0.95[out]`);
    }
    ff([...inputs, '-filter_complex', fl.join(';'), '-map', '[out]', '-t', String(dur), '-c:a', 'aac', '-b:a', '192k', out]);
}

writeFileSync(join(HERE, 'timing.js'), '/* Generated by prep-audio.mjs. Do not edit. */\nwindow.TIMING = ' + JSON.stringify(timing) + ';\n');
console.log('timing.js written');
