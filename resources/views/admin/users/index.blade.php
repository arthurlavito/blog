@extends('layouts.app')

@section('content')
<div class="py-12 bg-gray-50/50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Header --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
            <div>
                <h1 class="text-3xl font-black text-gray-900 tracking-tight">User Management</h1>
                <p class="text-sm text-gray-500 font-medium">Control roles and review author applications</p>
            </div>
            
            <div class="flex items-center space-x-2 bg-white p-1 rounded-2xl shadow-sm border border-gray-100">
                <span class="px-4 py-2 text-xs font-black uppercase text-indigo-600 bg-indigo-50 rounded-xl">
                    Total: {{ $users->count() }} Users
                </span>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 bg-emerald-50 border border-emerald-100 text-emerald-700 text-sm font-bold rounded-2xl flex items-center animate-fade-in">
                <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                {{ session('success') }}
            </div>
        @endif

        {{-- Users List --}}
        <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50/50 border-b border-gray-100">
                            <th class="px-8 py-5 text-[10px] font-black uppercase tracking-widest text-gray-400">User Details</th>
                            <th class="px-8 py-5 text-[10px] font-black uppercase tracking-widest text-gray-400">Current Role</th>
                            <th class="px-8 py-5 text-[10px] font-black uppercase tracking-widest text-gray-400">Application Status</th>
                            <th class="px-8 py-5 text-[10px] font-black uppercase tracking-widest text-gray-400 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($users as $user)
                        <tr class="hover:bg-gray-50/30 transition-colors">
                            {{-- Info --}}
                            <td class="px-8 py-6">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-[#4B0082] flex items-center justify-center text-white font-bold text-sm shadow-sm">
                                        {{ substr($user->name, 0, 1) }}
                                    </div>
                                    <div class="ml-4">
                                        <p class="text-sm font-bold text-gray-800">{{ $user->name }}</p>
                                        <p class="text-[11px] text-gray-400 font-medium">{{ $user->email }}</p>
                                    </div>
                                </div>
                            </td>

                            {{-- Role Badge --}}
                            <td class="px-8 py-6">
                                @php
                                    $roleColor = [
                                        'admin' => 'bg-rose-50 text-rose-600 border-rose-100',
                                        'author' => 'bg-indigo-50 text-indigo-600 border-indigo-100',
                                        'reader' => 'bg-gray-50 text-gray-500 border-gray-100',
                                    ][$user->role] ?? 'bg-gray-50 text-gray-500';
                                @endphp
                                <span class="px-3 py-1 text-[10px] font-black uppercase tracking-tighter border rounded-lg {{ $roleColor }}">
                                    {{ $user->role }}
                                </span>
                            </td>

                            {{-- Request Status --}}
                            <td class="px-8 py-6">
                                @if($user->author_requested_at && $user->role === 'reader')
                                    <div class="flex items-center text-amber-600">
                                        <span class="relative flex h-2 w-2 mr-2">
                                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                                            <span class="relative inline-flex rounded-full h-2 w-2 bg-amber-500"></span>
                                        </span>
                                        <span class="text-[11px] font-bold">Pending Review</span>
                                    </div>
                                @else
                                    <span class="text-[11px] text-gray-300 font-medium">—</span>
                                @endif
                            </td>

                            {{-- Actions --}}
                            <td class="px-8 py-6 text-right">
                                <div class="flex justify-end items-center space-x-3">
                                    
                                    {{-- CASE 1: Pending Author Request --}}
                                    @if($user->author_requested_at && $user->role === 'reader')
                                        <form action="{{ route('admin.author.approve', $user) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="bg-emerald-500 text-white px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-emerald-600 transition shadow-lg shadow-emerald-50">
                                                Approve
                                            </button>
                                        </form>

                                        <form action="{{ route('admin.author.deny', $user) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="bg-gray-100 text-gray-400 px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-rose-50 hover:text-rose-500 transition">
                                                Deny
                                            </button>
                                        </form>

                                    {{-- CASE 2: Already an Author (Show Demote) --}}
                                    @elseif($user->role === 'author')
                                        <form action="{{ route('admin.author.demote', $user) }}" method="POST" onsubmit="return confirm('Demote this author back to reader?')">
                                            @csrf
                                            <button type="submit" class="text-[10px] font-black uppercase tracking-widest text-amber-500 hover:text-amber-600 bg-amber-50 px-3 py-2 rounded-xl transition">
                                                Demote to Reader
                                            </button>
                                        </form>
                                    @endif

                                    {{-- Global Action: Delete User --}}
                                    @if($user->id !== auth()->id() && $user->role !== 'admin')
                                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('CRITICAL: This will delete the user and ALL their posts. Continue?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 text-gray-300 hover:text-rose-500 transition" title="Delete Account">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            {{-- Footer Space --}}
            <div class="px-8 py-6 bg-gray-50/30 border-t border-gray-100">
                {{-- If you use pagination later: {{ $users->links() }} --}}
            </div>
        </div>
    </div>
</div>
@endsection