<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cymae Beach Resort | Pasacao, Camarines Sur</title>
    <link rel="shortcut icon" href="images/logo.png" type="image/x-icon">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        html { scroll-behavior: smooth; }
    </style>
</head>
<body class="font-sans text-gray-800 bg-blue-50">

    <nav class="bg-white/90 backdrop-blur-md shadow-md fixed w-full z-50">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <a href="#" class="flex items-center space-x-2">
                    <img src="images/logo.png" alt="Cymae Logo" class="h-10 w-10 rounded-full object-cover hidden">
                    
                    <div class="bg-cyan-500 text-white p-2 rounded-full">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    </div>

                    <span class="font-bold text-2xl text-cyan-700 tracking-wide uppercase">Cymae Beach Resort</span>
                </a>

                <div class="hidden md:flex items-center space-x-6">
                    <a href="#home" class="text-gray-600 hover:text-cyan-600 font-medium transition">Home</a>
                    <a href="#cottages" class="text-gray-600 hover:text-cyan-600 font-medium transition">Cottages</a>
                    <a href="#amenities" class="text-gray-600 hover:text-cyan-600 font-medium transition">Amenities</a>
                </div>

                <div class="hidden md:flex items-center space-x-3">
                    <a href="login.php" class="text-cyan-700 font-medium hover:text-cyan-900 transition">Log In</a>
                    <a href="register.php" class="px-5 py-2 bg-cyan-500 hover:bg-cyan-600 text-white font-medium rounded-full shadow-md transition transform hover:scale-105">Book Now</a>
                </div>

                <button class="md:hidden outline-none mobile-menu-button">
                    <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
            </div>
        </div>
        <div class="hidden mobile-menu md:hidden bg-white border-t p-4">
            <a href="#home" class="block py-2 text-gray-600">Home</a>
            <a href="#cottages" class="block py-2 text-gray-600">Cottages</a>
            <a href="login.php" class="block py-2 text-cyan-600 font-bold">Log In</a>
            <a href="register.php" class="block py-2 text-cyan-600 font-bold">Sign Up</a>
        </div>
    </nav>

    <section id="home" class="relative h-screen flex items-center justify-center">
        <div class="absolute inset-0 z-0">
            <img src="images/bg.jpg" 
                 alt="Cymae Beach Resort Background" class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-black/40"></div>
        </div>

        <div class="relative z-10 text-center px-4 mt-16">
            <h1 class="text-4xl md:text-7xl font-extrabold text-white mb-2 drop-shadow-xl font-serif">
                CYMAE BEACH RESORT
            </h1>
            <p class="text-lg md:text-xl text-white/90 mb-8 font-light tracking-widest uppercase">
                Zone 3A Caranan Pasacao, Camarines Sur
            </p>
            <a href="register.php" class="inline-block px-8 py-4 bg-yellow-400 hover:bg-yellow-500 text-gray-900 font-bold rounded-full text-lg shadow-xl transition transform hover:-translate-y-1">
                Book Your Cottage
            </a>
        </div>
    </section>

    <section id="cottages" class="py-20 bg-white">
        <div class="container mx-auto px-6">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-800">Relax in Style</h2>
                <p class="text-gray-500 mt-4">Experience the heat of summer with our cozy cottages.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
                <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-2xl transition duration-300 border border-gray-100">
                    <img src="https://images.unsplash.com/photo-1499793983690-e29da59ef1c2?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" alt="Beachfront Cottage" class="w-full h-48 object-cover">
                    <div class="p-6">
                        <h3 class="font-bold text-xl mb-2 text-gray-800">Open Cottage (Large)</h3>
                        <p class="text-gray-600 text-sm mb-4">Perfect for family reunions. Located right by the shore.</p>
                        <div class="flex justify-between items-center">
                            <span class="text-cyan-600 font-bold">₱1,500 / day</span>
                            <a href="login.php" class="text-sm bg-cyan-100 text-cyan-700 py-1 px-3 rounded hover:bg-cyan-200">Book</a>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-2xl transition duration-300 border border-gray-100">
                    <img src="https://images.unsplash.com/photo-1582719508461-905c673771fd?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" alt="Family Villa" class="w-full h-48 object-cover">
                    <div class="p-6">
                        <h3 class="font-bold text-xl mb-2 text-gray-800">Open Cottage (Small)</h3>
                        <p class="text-gray-600 text-sm mb-4">Ideal for small groups or couples enjoying the breeze.</p>
                        <div class="flex justify-between items-center">
                            <span class="text-cyan-600 font-bold">₱800 / day</span>
                            <a href="login.php" class="text-sm bg-cyan-100 text-cyan-700 py-1 px-3 rounded hover:bg-cyan-200">Book</a>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-2xl transition duration-300 border border-gray-100">
                    <img src="https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" alt="Luxury Suite" class="w-full h-48 object-cover">
                    <div class="p-6">
                        <h3 class="font-bold text-xl mb-2 text-gray-800">Air-Conditioned Room</h3>
                        <p class="text-gray-600 text-sm mb-4">Stay cool and comfortable with our overnight rooms.</p>
                        <div class="flex justify-between items-center">
                            <span class="text-cyan-600 font-bold">₱2,500 / night</span>
                            <a href="login.php" class="text-sm bg-cyan-100 text-cyan-700 py-1 px-3 rounded hover:bg-cyan-200">Book</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="amenities" class="bg-cyan-50 py-20">
        <div class="container mx-auto px-6">
            <h2 class="text-3xl md:text-4xl font-bold text-center text-gray-800 mb-12">Resort Amenities</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                <div class="p-4 bg-white rounded shadow-sm">
                    <span class="text-4xl mb-4 block">🌊</span>
                    <h3 class="font-bold">Beach Access</h3>
                </div>
                <div class="p-4 bg-white rounded shadow-sm">
                    <span class="text-4xl mb-4 block">🎤</span>
                    <h3 class="font-bold">Videoke Rental</h3>
                </div>
                <div class="p-4 bg-white rounded shadow-sm">
                    <span class="text-4xl mb-4 block">🍖</span>
                    <h3 class="font-bold">Grilling Station</h3>
                </div>
                <div class="p-4 bg-white rounded shadow-sm">
                    <span class="text-4xl mb-4 block">🚿</span>
                    <h3 class="font-bold">Shower Rooms</h3>
                </div>
            </div>
        </div>
    </section>

    <footer class="bg-gray-900 text-white py-10">
        <div class="container mx-auto px-6 flex flex-col md:flex-row justify-between items-center">
            <div class="mb-6 md:mb-0">
                <span class="font-bold text-2xl text-cyan-400">Cymae Beach Resort</span>
                <p class="text-gray-400 mt-2">Zone 3A Caranan Pasacao, Camarines Sur</p>
            </div>
            <div class="flex space-x-6">
                <a href="#" class="text-gray-400 hover:text-white">Facebook</a>
                <a href="#" class="text-gray-400 hover:text-white">Contact Us</a>
            </div>
        </div>
        <div class="text-center mt-8 text-gray-600 text-sm">
            &copy; <?php echo date("Y"); ?> Cymae Beach Resort Booking System. All rights reserved.
        </div>
    </footer>

    <script>
        const btn = document.querySelector('button.mobile-menu-button');
        const menu = document.querySelector('.mobile-menu');
        btn.addEventListener('click', () => {
            menu.classList.toggle('hidden');
        });
    </script>
</body>
</html>