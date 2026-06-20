@extends('layouts.app')

@section('title', 'Contact Anim24 — Send Tips, Feedback & Enquiries')
@section('meta_description', 'Reach out to Anim24 with news tips, editorial corrections, author access requests, partnership enquiries, or general support messages.')
@section('canonical', route('contact'))

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

        @if(session('success'))
            <div class="mb-6 px-6 py-4 bg-emerald-50 border border-emerald-200 rounded-2xl text-emerald-700 text-sm font-bold">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('contact.send') }}" method="POST" class="space-y-6">
            @csrf

            <div>
                <label for="name" class="block text-[10px] font-black uppercase tracking-widest text-gray-500 mb-2">Name</label>
                <input id="name" name="name" type="text" value="{{ old('name') }}" class="w-full rounded-2xl border-gray-200 focus:border-[#4B0082] focus:ring-[#4B0082]" required>
                @error('name')<p class="mt-1 text-[10px] font-bold text-rose-500">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="email" class="block text-[10px] font-black uppercase tracking-widest text-gray-500 mb-2">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" class="w-full rounded-2xl border-gray-200 focus:border-[#4B0082] focus:ring-[#4B0082]" required>
                @error('email')<p class="mt-1 text-[10px] font-bold text-rose-500">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="topic" class="block text-[10px] font-black uppercase tracking-widest text-gray-500 mb-2">Topic</label>
                <select id="topic" name="topic" class="w-full rounded-2xl border-gray-200 focus:border-[#4B0082] focus:ring-[#4B0082]">
                    <option {{ old('topic') === 'General question' ? 'selected' : '' }}>General question</option>
                    <option {{ old('topic') === 'News tip' ? 'selected' : '' }}>News tip</option>
                    <option {{ old('topic') === 'Correction' ? 'selected' : '' }}>Correction</option>
                    <option {{ old('topic') === 'Author request' ? 'selected' : '' }}>Author request</option>
                    <option {{ old('topic') === 'Partnership' ? 'selected' : '' }}>Partnership</option>
                </select>
            </div>

            <div>
                <label for="message" class="block text-[10px] font-black uppercase tracking-widest text-gray-500 mb-2">Message</label>
                <textarea id="message" name="message" rows="7" class="w-full rounded-2xl border-gray-200 focus:border-[#4B0082] focus:ring-[#4B0082]" required>{{ old('message') }}</textarea>
                @error('message')<p class="mt-1 text-[10px] font-bold text-rose-500">{{ $message }}</p>@enderror
            </div>

            <button type="submit" class="inline-flex items-center justify-center px-8 py-4 rounded-2xl bg-[#4B0082] text-white text-[10px] font-black uppercase tracking-widest hover:bg-indigo-700 transition">
                Send Message
            </button>
        </form>
    </section>
</div>
@endsection
