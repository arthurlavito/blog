@extends('layouts.app')

@section('title', 'Contact | ' . config('app.name', 'Anim24.com'))

@section('content')
<div class="grid gap-8 lg:grid-cols-[0.9fr_1.1fr]">
    <section class="bg-white rounded-[2rem] border border-gray-100 shadow-sm p-8 sm:p-10">
        <p class="text-indigo-500 text-[10px] font-black uppercase tracking-[0.3em] mb-4">Contact</p>
        <h1 class="text-4xl sm:text-5xl font-black text-gray-900 tracking-tighter mb-6">
            Send us news, questions, or feedback.
        </h1>
        <p class="text-gray-500 font-medium leading-relaxed mb-8">
            Reach out about editorial tips, author access, corrections, partnerships, or general Anim24 support.
        </p>

        <div class="space-y-4">
            <div class="rounded-2xl bg-gray-50 border border-gray-100 p-5">
                <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1">Email</p>
                <a href="mailto:contact@anim24.com" class="text-lg font-black text-[#4B0082] hover:text-indigo-500">
                    contact@anim24.com
                </a>
            </div>
            <div class="rounded-2xl bg-gray-50 border border-gray-100 p-5">
                <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1">Response Time</p>
                <p class="text-sm font-bold text-gray-700">Most messages are reviewed within 2 business days.</p>
            </div>
        </div>
    </section>

    <section class="bg-white rounded-[2rem] border border-gray-100 shadow-sm p-8 sm:p-10">
        <form action="mailto:contact@anim24.com" method="POST" enctype="text/plain" class="space-y-6">
            <div>
                <label for="name" class="block text-[10px] font-black uppercase tracking-widest text-gray-500 mb-2">Name</label>
                <input id="name" name="name" type="text" class="w-full rounded-2xl border-gray-200 focus:border-[#4B0082] focus:ring-[#4B0082]" required>
            </div>

            <div>
                <label for="email" class="block text-[10px] font-black uppercase tracking-widest text-gray-500 mb-2">Email</label>
                <input id="email" name="email" type="email" class="w-full rounded-2xl border-gray-200 focus:border-[#4B0082] focus:ring-[#4B0082]" required>
            </div>

            <div>
                <label for="topic" class="block text-[10px] font-black uppercase tracking-widest text-gray-500 mb-2">Topic</label>
                <select id="topic" name="topic" class="w-full rounded-2xl border-gray-200 focus:border-[#4B0082] focus:ring-[#4B0082]">
                    <option>General question</option>
                    <option>News tip</option>
                    <option>Correction</option>
                    <option>Author request</option>
                    <option>Partnership</option>
                </select>
            </div>

            <div>
                <label for="message" class="block text-[10px] font-black uppercase tracking-widest text-gray-500 mb-2">Message</label>
                <textarea id="message" name="message" rows="7" class="w-full rounded-2xl border-gray-200 focus:border-[#4B0082] focus:ring-[#4B0082]" required></textarea>
            </div>

            <button type="submit" class="inline-flex items-center justify-center px-8 py-4 rounded-2xl bg-[#4B0082] text-white text-[10px] font-black uppercase tracking-widest hover:bg-indigo-700 transition">
                Send Message
            </button>
        </form>
    </section>
</div>
@endsection
