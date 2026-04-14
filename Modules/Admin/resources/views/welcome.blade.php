<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome - FoodDelivery Pro</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#4f46e5',
                        primary_dark: '#4338ca',
                        success: '#10b981',
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('modules/admin/css/auth.css') }}">
</head>
<body class="bg-gray-50 text-gray-800 font-sans antialiased min-h-screen flex items-center justify-center p-4">

    <!-- Welcome Card -->
    <div class="bg-white w-full max-w-lg rounded-2xl shadow-xl border border-gray-100 overflow-hidden text-center scale-in">
        
        <div class="pt-12 px-8 pb-10">
            <!-- Celebratory Icon -->
            <div class="flex justify-center mb-6 fade-up">
                <div class="bg-green-50 p-4 rounded-full border-2 border-green-100">
                    <svg class="w-12 h-12 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>

            <h1 class="text-3xl font-extrabold text-gray-900 mb-2 fade-up">Account Created!</h1>
            <p class="text-gray-500 mb-8 max-w-sm mx-auto fade-up">Welcome to FoodDelivery Pro. Your profile has been successfully generated and securely encrypted.</p>

            <!-- Next Steps -->
            <div class="bg-gray-50 rounded-xl p-6 text-left border border-gray-100 mb-8">
                <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wide mb-4 stagger-1">Next steps to complete profile</h3>
                
                <ul class="space-y-4">
                    <li class="flex items-start stagger-1">
                        <div class="flex-shrink-0 mt-0.5">
                            <div class="h-5 w-5 rounded-full bg-primary flex items-center justify-center">
                                <svg class="h-3 w-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg>
                            </div>
                        </div>
                        <p class="ml-3 text-sm text-gray-700">Verify your primary email address.</p>
                    </li>
                    
                    <li class="flex items-start stagger-2">
                        <div class="flex-shrink-0 mt-0.5">
                            <div class="h-5 w-5 rounded-full bg-gray-200 border border-gray-300 flex items-center justify-center">
                                <span class="text-xs font-bold text-gray-500 shadow-none">2</span>
                            </div>
                        </div>
                        <p class="ml-3 text-sm text-gray-500">Upload an Avatar or Business Logo.</p>
                    </li>

                    <li class="flex items-start stagger-3">
                        <div class="flex-shrink-0 mt-0.5">
                            <div class="h-5 w-5 rounded-full bg-gray-200 border border-gray-300 flex items-center justify-center">
                                <span class="text-xs font-bold text-gray-500 shadow-none">3</span>
                            </div>
                        </div>
                        <p class="ml-3 text-sm text-gray-500">Link your primary payout checking account.</p>
                    </li>
                </ul>
            </div>

            <!-- Action Area -->
            <div class="stagger-3 mt-4">
                <a href="{{ route('admin.dashboard') }}" class="w-full flex justify-center items-center py-4 px-4 border border-transparent rounded-lg shadow-sm text-base font-bold text-white bg-primary hover:bg-primary_dark focus:outline-none transition-all group">
                    Continue to Dashboard
                    <svg class="ml-2 w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </a>
            </div>
        </div>
    </div>

</body>
</html>
