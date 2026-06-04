@extends('layouts.app')

@section('title', 'Privacy Policy | ' . config('app.name', 'Anim24.com'))

@section('content')
<article class="bg-white rounded-[2rem] border border-gray-100 shadow-sm p-8 sm:p-10 lg:p-14">
    <div class="max-w-3xl">
        <p class="text-indigo-500 text-[10px] font-black uppercase tracking-[0.3em] mb-4">Privacy Policy</p>
        <h1 class="text-4xl sm:text-5xl font-black text-gray-900 tracking-tighter mb-4">How Anim24 handles your information.</h1>
        <p class="text-sm font-bold text-gray-400 mb-10">Last updated: {{ now()->format('F j, Y') }}</p>
    </div>

    <div class="prose prose-indigo max-w-none prose-headings:font-black prose-headings:text-gray-900 prose-p:text-gray-600 prose-p:font-medium prose-li:text-gray-600 prose-li:font-medium">
        <h2>Information We Collect</h2>
        <p>
            Anim24 may collect account details such as your name, email address, profile activity, submitted posts,
            comments, likes, and author requests when you use the site.
        </p>

        <h2>How We Use Information</h2>
        <p>
            We use this information to operate the blog, display published content, manage accounts, review author
            requests, moderate comments, improve site reliability, and respond to support messages.
        </p>

        <h2>Cookies And Sessions</h2>
        <p>
            The site uses cookies and session storage for authentication, security, preferences and normal
            application behavior. Disabling cookies may prevent login and account features from working correctly.
        </p>

        <h2>Content You Submit</h2>
        <p>
            Posts, comments, names, and public profile details may be visible to other users when you publish or
            interact with content. Do not submit private information that you do not want displayed publicly.
        </p>

        <h2>Data Sharing</h2>
        <p>
            We do not sell personal information. We may share limited information only when required to operate the
            service, comply with legal obligations, prevent abuse, or protect the rights and safety of Anim24 users.
        </p>

        <h2>Contact</h2>
        <p>
            For privacy questions or account-related requests, contact us at
            <a href="mailto:contact@anim24.com">contact@anim24.com</a>.
        </p>
    </div>
</article>
@endsection
