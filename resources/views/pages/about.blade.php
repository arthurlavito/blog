@extends('layouts.app')

@section('title', 'About | ' . config('app.name', 'Anim24.com'))

@section('content')
<div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm overflow-hidden">
    <div class="bg-[#4B0082] px-6 py-16 sm:px-10 lg:px-16">
        <p class="text-indigo-200 text-[10px] font-black uppercase tracking-[0.3em] mb-4">About Anim24</p>
        <h1 class="text-4xl sm:text-5xl font-black text-white tracking-tighter max-w-3xl">
            Redefining Global Information for a Connected World
        </h1>
    </div>

    <div class="px-6 py-12 sm:px-10 lg:px-16 grid gap-10 lg:grid-cols-[1.2fr_0.8fr]">
        <div class="space-y-6 text-gray-600 leading-relaxed font-medium">
            <p>
                Anim24 is an open-access global news and analysis platform dedicated to delivering unfiltered breaking coverage and deep investigative insights from a decentralized network of voices.
            </p>
           
            <p>
                In an era where traditional media architectures often struggle to keep pace with a rapidly changing world, Anim24 stands as a digital powerhouse rewriting the rules of information distribution. We operate dynamic coverage at the sharp intersection of rapid breaking news and hyper-focused investigative essays. Spanning critical topics from international geopolitics to emerging economic trends, our platform utilizes an agile data and content framework designed to outpace old-guard regional bureaus. This active approach transforms Anim24 from a mere alternative source into a daily necessity for an expanding global audience.
            </p>
            
            <p>
                What sets Anim24 apart from legacy media institutions is our commitment to a crowd-verified infrastructure that deliberately bypasses traditional corporate editorial bottlenecks. While major network syndicates remain tethered to rigid broadcast schedules and geographical constraints, we rely on a distributed network of independent journalists, field analysts, and contributors who publish live insights around the clock. This model allows us to maintain a continuous, unyielding reporting cycle, capturing localized developments with an immediacy and raw granularity that heavily centralized newsrooms struggle to replicate.
            </p>
           
            <p>
                Operating a high-velocity, multilingual news ecosystem comes with a profound responsibility. At Anim24, we meet the challenges of the modern media landscape by continually refining our open-source verification pipelines, balancing the rapid speed our readers depend on with an uncompromised commitment to systemic truth. As modern media consumption continues to splinter, we believe audiences deserve a multi-perspective hub free from top-down directives. We are not just reporting the world as it happens, we are building a transparent ecosystem for public truth, open to all.
            </p>
        </div>

        <div class="bg-gray-50 rounded-3xl p-8 border border-gray-100">
    <h2 class="text-xl font-black text-gray-900 mb-6">What We Publish</h2>
    <ul class="space-y-4 text-sm font-bold text-gray-600">
        <li class="flex gap-3">
            <span class="mt-1 h-2 w-2 rounded-full bg-indigo-500 shrink-0"></span>
            Real-time breaking updates spanning global politics, international relations, and market-moving economic trends.
        </li>
        <li class="flex gap-3">
            <span class="mt-1 h-2 w-2 rounded-full bg-indigo-500 shrink-0"></span>
            Critical, ground-level coverage of regional stability, institutional reforms, and the ongoing security challenges across Nigeria.
        </li>
        <li class="flex gap-3">
            <span class="mt-1 h-2 w-2 rounded-full bg-indigo-500 shrink-0"></span>
            In-depth investigative journalism, policy analysis, and multi-perspective opinion pieces from independent voices.
        </li>
    </ul>
</div>
    </div>
</div>
@endsection
