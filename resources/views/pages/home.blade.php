{{--
    Homepage, launch version (UB Shield launch brief, 23 November).

    Hidden for launch — the components stay in the codebase so they can return
    once there is real, verified content behind them (brief §8):
      <x-sections.trust />       credentials placeholders and sample client logos
      <x-sections.soc />         24/7 SOC claim and the sample incident feed
      <x-sections.frameworks />  DPDPA / RBI / SEBI / PCI DSS / HIPAA marketing
      <x-sections.cases />       placeholder case studies and stats
      <x-sections.insights />    field notes with placeholder dates
--}}
<x-layout preview :schema="$schema">
    <x-sections.hero />
    <x-sections.services />
    <x-sections.statement />
    <x-sections.process />
    <x-sections.industries />
    <x-sections.strip />
</x-layout>
