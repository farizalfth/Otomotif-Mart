<header class="bg-white border-b shadow-sm sticky top-0 z-50">
    <div class="max-w-6xl mx-auto px-4 py-4 flex justify-between items-center">
        <!-- Brand -->
        <a class="text-xl font-bold text-gray-800 hover:text-blue-600 transition-colors" href="/">
            Bandung Jaya Motor
        </a>

        <!-- Mobile Burger Icon -->
        <div class="lg:hidden flex items-center">
            <button id="burger-icon" class="text-gray-600 focus:outline-none">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>
        </div>

        <!-- Navigation -->
        <div class="hidden lg:flex items-center space-x-6">
            <a class="{{request()->is('/')?'text-blue-600 font-medium': 'text-gray-600 hover:text-blue-600'}} transition-colors" href="/">
                Home
            </a>
            <a class="{{request()->is('categories')?'text-blue-600 font-medium': 'text-gray-600 hover:text-blue-600'}} transition-colors" href="/categories">
                Categories
            </a>
            <a class="{{request()->is('products')?'text-blue-600 font-medium': 'text-gray-600 hover:text-blue-600'}} transition-colors" href="/products">
                Products
            </a>
            
            <!-- Cart -->
            <a class="{{request()->is('cart')?'text-blue-600 font-medium': 'text-gray-600 hover:text-blue-600'}} transition-colors" href="/cart">
                Cart ({{$total_count}})
            </a>

            <!-- Auth Section -->
            @guest
            <a class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors text-sm font-medium" href="/login">
                Login
            </a>
            @endguest

            @auth
            <div class="flex items-center space-x-4 text-sm">
                <a class="text-gray-600 hover:text-blue-600 transition-colors" href="/my-orders">
                    My Orders
                </a>
                <span class="text-gray-700 font-medium">
                    {{auth()->user()->name}}
                </span>
                <a class="text-gray-600 hover:text-blue-600 transition-colors" href="/logout">
                    Logout
                </a>
            </div>
            @endauth
        </div>
    </div>

    <!-- Mobile Menu -->
    <div id="mobile-menu" class="lg:hidden flex flex-col space-y-4 p-4 bg-white border-t mt-4 hidden">
        <a class="{{request()->is('/')?'text-blue-600 font-medium': 'text-gray-600 hover:text-blue-600'}} transition-colors" href="/">
            Home
        </a>
        <a class="{{request()->is('categories')?'text-blue-600 font-medium': 'text-gray-600 hover:text-blue-600'}} transition-colors" href="/categories">
            Categories
        </a>
        <a class="{{request()->is('products')?'text-blue-600 font-medium': 'text-gray-600 hover:text-blue-600'}} transition-colors" href="/products">
            Products
        </a>

        <!-- Cart -->
        <a class="{{request()->is('cart')?'text-blue-600 font-medium': 'text-gray-600 hover:text-blue-600'}} transition-colors" href="/cart">
            Cart ({{$total_count}})
        </a>

        <!-- Auth Section -->
        @guest
        <a class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors text-sm font-medium" href="/login">
            Login
        </a>
        @endguest

        @auth
        <div class="flex items-center space-x-4 text-sm">
            <a class="text-gray-600 hover:text-blue-600 transition-colors" href="/my-orders">
                My Orders
            </a>
            <span class="text-gray-700 font-medium">
                {{auth()->user()->name}}
            </span>
            <a class="text-gray-600 hover:text-blue-600 transition-colors" href="/logout">
                Logout
            </a>
        </div>
        @endauth
    </div>
</header>

<script>
    // Toggle mobile menu
    document.getElementById('burger-icon').addEventListener('click', () => {
        const menu = document.getElementById('mobile-menu');
        menu.classList.toggle('hidden');
    });
</script>
