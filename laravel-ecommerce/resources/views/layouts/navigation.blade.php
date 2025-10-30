<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ url('/') }}">
                        <h1 class="text-xl font-bold text-gray-800">Your Store</h1>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                @auth
                @if (Auth::user()->role === 'admin')

                    <!-- Admin Panel Links -->
                    <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                        {{ __('Admin Dashboard') }}
                    </x-nav-link>

                    <x-nav-link :href="route('admin.users')" :active="request()->routeIs('admin.users')">
                        {{ __('Manage Users') }}
                    </x-nav-link>

                    <x-nav-link :href="route('admin.categories.index')" :active="request()->routeIs('admin.categories.*')">
                        {{ __('Manage Categories') }}
                    </x-nav-link>

                    <x-nav-link :href="route('admin.products.index')" :active="request()->routeIs('admin.products.*')">
                        {{ __('Manage Products') }}
                    </x-nav-link>

                    <x-nav-link :href="route('admin.orders')" :active="request()->routeIs('admin.orders.*')">
                        {{ __('Manage Orders') }}
                    </x-nav-link>

            @else
                <!-- Product List -->
                <x-nav-link :href="route('products.index')" :active="request()->routeIs('products.index')">
                    {{ __('All Products') }}
                </x-nav-link>

                <!-- Customer Dashboard -->
                <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                    {{ __('My Dashboard') }}
                </x-nav-link>
                <!-- Cart -->
                <x-nav-link :href="route('cart.index')" :active="request()->routeIs('cart.index')">
                    {{ __('Cart') }}
                </x-nav-link>

                <x-nav-link :href="route('wishlist.index')" :active="request()->routeIs('wishlist.index')">
                    {{ __('Wishlist')}}
                </x-nav-link>

                <x-nav-link :href="route('orders')" :active="request()->routeIs('orders')">
                    {{ __('My Orders')}}
                </x-nav-link>

            @endif
            @endauth
        </div>
                
    </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                @auth
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none">
                                <div>{{ Auth::user()->name }}</div>
                                <div class="ms-1">
                                    <svg class="fill-current h-4 w-4" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 011.08 1.04l-4.25 4.25a.75.75 0 01-1.08 0L5.21 8.27a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile.edit')">
                                {{ __('Profile') }}
                            </x-dropdown-link>
                            @auth
                                @if (Auth::user()->role === 'admin')
                                    <x-dropdown-link :href="route('admin.coupons.index')">
                                        {{ __('Coupons') }}
                                    </x-dropdown-link>
                                @endif
                            @endauth

                            <!-- Logout -->
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                        onclick="event.preventDefault();
                                                    this.closest('form').submit();">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                @endauth
            </div>
        </div>
    </div>
</nav>
