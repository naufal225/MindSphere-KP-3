<!-- Sidebar -->
<div class="fixed inset-y-0 left-0 z-50 flex flex-col w-64 text-white transition-transform duration-300 ease-in-out transform -translate-x-full sidebar-container bg-[#1E40AF] shadow-medium lg:relative lg:translate-x-0"
    id="sidebar">
    <div class="flex items-center justify-between px-6 py-4 bg-[#2563EB]">
        <div class="w-full">
            <div class="flex justify-center">
                <img src="{{ asset('img/logo.png') }}" alt="MindSphere Logo" class="rounded-lg shadow-lg w-16 h-16">
            </div>
            <h1 class="mt-2 text-lg font-bold text-center text-white">KeepItGrow</h1>
        </div>
        <button class="text-white lg:hidden hover:text-blue-200" onclick="toggleSidebar()">
            <i class="text-lg fas fa-times"></i>
        </button>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto sidebar-scroll">
        <a href="{{ route('admin.dashboard') }}"
            class="flex items-center px-4 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('admin.dashboard') ? 'bg-[#2563EB] text-white shadow-soft' : 'text-blue-100 hover:bg-[#2563EB] hover:text-white' }}">
            <i class="w-5 mr-3 text-center fas fa-tachometer-alt"></i>
            <span class="font-medium">Dashboard</span>
        </a>

        <!-- Data Users -->
        <a href="{{ route('admin.users.index') }}"
            class="flex items-center px-4 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('admin.users.*') ? 'bg-[#2563EB] text-white shadow-soft' : 'text-blue-100 hover:bg-[#2563EB] hover:text-white' }}">
            <i class="w-5 mr-3 text-center fas fa-users"></i>
            <span class="font-medium">Data Users</span>
        </a>

        <!-- Divisi -->
        <a href="{{ route('admin.divisions.index') }}"
            class="flex items-center px-4 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('admin.divisions.*') ? 'bg-[#2563EB] text-white shadow-soft' : 'text-blue-100 hover:bg-[#2563EB] hover:text-white' }}">
            <i class="w-5 mr-3 text-center fas fa-graduation-cap"></i>
            <span class="font-medium">Divisi</span>
        </a>

        <!-- Categories -->
        <a href="{{ route('admin.categories.index') }}"
            class="flex items-center px-4 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('admin.categories.*') ? 'bg-[#2563EB] text-white shadow-soft' : 'text-blue-100 hover:bg-[#2563EB] hover:text-white' }}">
            <i class="w-5 mr-3 text-center fas fa-tags"></i>
            <span class="font-medium">Kategori</span>
        </a>

        <!-- Challenges -->
        <a href="{{ route('admin.challenges.index') }}"
            class="flex items-center px-4 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('admin.challenges.*') ? 'bg-[#2563EB] text-white shadow-soft' : 'text-blue-100 hover:bg-[#2563EB] hover:text-white' }}">
            <i class="w-5 mr-3 text-center fas fa-flag-checkered"></i>
            <span class="font-medium">Challenges</span>
        </a>

        <!-- Habits -->
        <a href="{{ route('admin.habits.index') }}"
            class="flex items-center px-4 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('admin.habits.*') ? 'bg-[#2563EB] text-white shadow-soft' : 'text-blue-100 hover:bg-[#2563EB] hover:text-white' }}">
            <i class="w-5 mr-3 text-center fas fa-sync-alt"></i>
            <span class="font-medium">Habits</span>
        </a>

        <!-- Reward -->
        <div class="space-y-1"
            x-data="{ open: {{ request()->routeIs('admin.rewards.*', 'admin.requests.*') ? 'true' : 'false' }} }">
            <button type="button"
                class="flex items-center w-full px-4 py-3 text-left transition-all duration-200 rounded-lg"
                :class="open ? 'bg-[#2563EB] text-white shadow-soft' : 'text-blue-100 hover:bg-[#2563EB] hover:text-white'"
                @click="open = !open">
                <i class="w-5 mr-3 text-center fas fa-gift"></i>
                <span class="flex-1 font-medium">Reward</span>
                <i class="text-xs transition-transform duration-200 fas fa-chevron-down"
                    :class="{'rotate-180': open}"></i>
            </button>
            <div class="pl-4 space-y-1 overflow-hidden" x-show="open" x-collapse>
                <a href="{{ route('admin.rewards.index') }}"
                    class="flex items-center px-4 py-2 rounded-lg text-sm transition-all duration-200 {{ request()->routeIs('admin.rewards.index') ? 'bg-[#3B82F6] text-white' : 'text-blue-200 hover:bg-[#2563EB] hover:text-white' }}">
                    <i class="w-4 mr-2 text-center fas fa-list"></i>
                    <span>Manage Rewards</span>
                </a>
                <a href="{{ route('admin.requests.index') }}"
                    class="flex items-center px-4 py-2 rounded-lg text-sm transition-all duration-200 {{ request()->routeIs('admin.requests.*') ? 'bg-[#3B82F6] text-white' : 'text-blue-200 hover:bg-[#2563EB] hover:text-white' }}">
                    <i class="w-4 mr-2 text-center fas fa-hand-paper"></i>
                    <span>Reward Requests</span>
                </a>
            </div>
        </div>

        <!-- Reports & Analytics -->
        <div class="space-y-1"
            x-data="{ open: {{ request()->routeIs('admin.reports.*', 'admin.analytics.*') ? 'true' : 'false' }} }">
            <button type="button"
                class="flex items-center w-full px-4 py-3 text-left transition-all duration-200 rounded-lg"
                :class="open ? 'bg-[#2563EB] text-white shadow-soft' : 'text-blue-100 hover:bg-[#2563EB] hover:text-white'"
                @click="open = !open">
                <i class="w-5 mr-3 text-center fas fa-chart-bar"></i>
                <span class="flex-1 font-medium">Reports & Analytics</span>
                <i class="text-xs transition-transform duration-200 fas fa-chevron-down"
                    :class="{'rotate-180': open}"></i>
            </button>
            <div class="pl-4 space-y-1 overflow-hidden" x-show="open" x-collapse>
                <a href="{{ route('admin.user-progress.index') }}"
                    class="flex items-center px-4 py-2 rounded-lg text-sm transition-all duration-200 {{ request()->routeIs('admin.user-progress.*') ? 'bg-[#3B82F6] text-white' : 'text-blue-200 hover:bg-[#2563EB] hover:text-white' }}">
                    <i class="w-4 mr-2 text-center fas fa-chart-line"></i>
                    <span>User Progress</span>
                </a>
                {{-- Fitur dibawah nyusul kapan kapan ajahh ;v --}}
                {{-- <a href=""
                    class="flex items-center px-4 py-2 rounded-lg text-sm transition-all duration-200 {{ request()->routeIs('admin.habit-stats.*') ? 'bg-[#3B82F6] text-white' : 'text-blue-200 hover:bg-[#2563EB] hover:text-white' }}">
                    <i class="w-4 mr-2 text-center fas fa-chart-pie"></i>
                    <span>Habit Statistics</span>
                </a>
                <a href=""
                    class="flex items-center px-4 py-2 rounded-lg text-sm transition-all duration-200 {{ request()->routeIs('admin.challenge-stats.*') ? 'bg-[#3B82F6] text-white' : 'text-blue-200 hover:bg-[#2563EB] hover:text-white' }}">
                    <i class="w-4 mr-2 text-center fas fa-trophy"></i>
                    <span>Challenge Completion</span>
                </a> --}}
            </div>
        </div>

        <!-- System Settings -->
        {{-- <div class="space-y-1" x-data="{ open: {{ request()->routeIs('admin.settings.*') ? 'true' : 'false' }} }">
            <button type="button"
                class="flex items-center w-full px-4 py-3 text-left transition-all duration-200 rounded-lg"
                :class="open ? 'bg-[#2563EB] text-white shadow-soft' : 'text-blue-100 hover:bg-[#2563EB] hover:text-white'"
                @click="open = !open">
                <i class="w-5 mr-3 text-center fas fa-cog"></i>
                <span class="flex-1 font-medium">System Settings</span>
                <i class="text-xs transition-transform duration-200 fas fa-chevron-down"
                    :class="{'rotate-180': open}"></i>
            </button>
            <div class="pl-4 space-y-1 overflow-hidden" x-show="open" x-collapse>
                <a href=""
                    class="flex items-center px-4 py-2 rounded-lg text-sm transition-all duration-200 {{ request()->routeIs('admin.general-settings.*') ? 'bg-[#3B82F6] text-white' : 'text-blue-200 hover:bg-[#2563EB] hover:text-white' }}">
                    <i class="w-4 mr-2 text-center fas fa-sliders-h"></i>
                    <span>General Settings</span>
                </a>
                <a href=""
                    class="flex items-center px-4 py-2 rounded-lg text-sm transition-all duration-200 {{ request()->routeIs('admin.points-settings.*') ? 'bg-[#3B82F6] text-white' : 'text-blue-200 hover:bg-[#2563EB] hover:text-white' }}">
                    <i class="w-4 mr-2 text-center fas fa-star"></i>
                    <span>Points System</span>
                </a>
                <a href=""
                    class="flex items-center px-4 py-2 rounded-lg text-sm transition-all duration-200 {{ request()->routeIs('admin.notification-settings.*') ? 'bg-[#3B82F6] text-white' : 'text-blue-200 hover:bg-[#2563EB] hover:text-white' }}">
                    <i class="w-4 mr-2 text-center fas fa-bell"></i>
                    <span>Notifications</span>
                </a>
            </div>
        </div> --}}
    </nav>

    <div class="p-4 border-t border-blue-600">
        <a class="flex items-center mb-4" href="{{ route('admin.profile.index') }}">
            <div class="flex items-center justify-center w-10 h-10 mr-3 rounded-full bg-[#2563EB]">
                @if(Auth::user()->avatar_url)
                <img class="object-cover w-10 h-10 rounded-full" src="{{ Storage::url(Auth::user()->avatar_url) }}"
                    alt="{{ Auth::user()->name }}">
                @else
                <div class="flex items-center justify-center w-10 h-10 bg-gray-300 rounded-full">
                    <span class="text-sm font-medium text-gray-700">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </span>
                </div>
                @endif
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-white truncate">{{ Auth::user()->name }}</p>
                <p class="text-xs text-blue-200">{{ Auth::user()->email }}</p>
            </div>
        </a>
        {{-- {{ route('logout') }} --}}
        <form action="/logout" method="POST">
            @csrf
            <button type="submit"
                class="flex items-center w-full px-4 py-2 transition-all duration-200 rounded-lg text-blue-100 hover:bg-[#2563EB] hover:text-white">
                <i class="w-5 mr-3 text-center fas fa-sign-out-alt"></i>
                <span class="font-medium">Logout</span>
            </button>
        </form>
    </div>
</div>
