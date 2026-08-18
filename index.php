<?php
include __DIR__ . '/src/helpers/url.php';
require_once __DIR__ . '/src/helpers/session.php';
startSession();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="TraceX helps you track expenses, manage budgets, and gain clear spending insights, all in one simple app.">
    <meta name="robots" content="index, follow">
    <meta name="google-site-verification" content="BjZtxF8_enY2XvKUNfcXDYfD2t6LLnWF2hTLNit3uQU" />
    <link rel="canonical" href="<?= htmlspecialchars($baseUrl) ?>">
    <meta property="og:title" content="TraceX - Smart expense tracker">
    <meta property="og:description" content="Track spending, manage budgets, and get clear insights with TraceX.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?= htmlspecialchars($baseUrl) ?>">
    <meta property="og:image" content="<?= htmlspecialchars(url('public/assets/test.png')) ?>">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="TraceX - Smart expense tracker">
    <meta name="twitter:description" content="Track spending, manage budgets, and get clear insights with TraceX.">
    <meta name="twitter:image" content="<?= htmlspecialchars(url('public/assets/test.png')) ?>">
    <script type="application/ld+json">
        <?= json_encode([
            [
                '@context' => 'https://schema.org',
                '@type' => 'Organization',
                'name' => 'TraceX',
                'url' => $baseUrl,
                'logo' => url('public/assets/test.png'),
                'sameAs' => [
                    'https://github.com/aungkyawthetx'
                ]
            ],
            [
                '@context' => 'https://schema.org',
                '@type' => 'WebSite',
                'name' => 'TraceX',
                'url' => $baseUrl
            ]
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?>
    </script>
    <title>TraceX - Smart expense tracker</title>
    <link rel="stylesheet" href="/src/output.css">
    <link rel="stylesheet" href="/public/assets/vendor/fontawesome-free-7.1.0-web/css/all.min.css">
    <link rel="icon" type="image/png" href="/public/assets/favicon.png">
</head>
<body class="bg-white text-gray-800 antialiased">
    <nav class="sticky top-0 z-50 border-b border-gray-200 bg-white/90 backdrop-blur">
        <div class="max-w-7xl mx-auto px-5 py-4 flex items-center justify-between">
            <a href="/" class="flex items-center gap-2">
                <img src="/public/assets/test.png" alt="TraceX Logo" class="h-9 rounded-lg">
            </a>

            <div class="hidden md:flex items-center gap-3">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="<?= url('public/index.php') ?>" class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-indigo-600">
                        Dashboard
                    </a>
                    <a href="<?= url('src/helpers/logout.php') ?>" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-all">
                        Logout
                    </a>
                <?php else: ?>
                    <a href="<?= url('login/index.php') ?>" class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-indigo-600">
                        Sign In
                    </a>
                    <a href="<?= url('register/index.php') ?>" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-all">
                        Get Started
                    </a>
                <?php endif; ?>
            </div>

            <button
                id="mobile-menu-btn"
                type="button"
                class="md:hidden inline-flex items-center justify-center w-10 h-10 rounded-lg text-gray-600 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                aria-controls="mobile-menu"
                aria-expanded="false"
                aria-label="Toggle menu">
                <i id="mobile-menu-icon" class="fas fa-bars text-lg"></i>
            </button>
        </div>

        <div id="mobile-menu" class="md:hidden overflow-hidden max-h-0 opacity-0 transition-all duration-300 ease-out border-t border-gray-100 bg-white">
            <div class="px-5 py-4 flex flex-col gap-3">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="<?= url('public/index.php') ?>" class="px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 rounded-lg">Dashboard</a>
                    <a href="<?= url('src/helpers/logout.php') ?>" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700">Logout</a>
                <?php else: ?>
                    <a href="<?= url('login/index.php') ?>" class="px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 rounded-lg">Sign In</a>
                    <a href="<?= url('register/index.php') ?>" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700">Get Started</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- hero section -->
    <section class="max-w-7xl mx-auto px-5 pt-16 pb-20 md:pt-24 md:pb-28">
        <div class="flex flex-col lg:flex-row items-center gap-14">
            <div class="flex-1 text-center lg:text-left">
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold tracking-tight text-gray-900 leading-tight">
                    Take Control of <br class="hidden sm:block">Every Expense with <span class="text-indigo-600">TraceX</span>
                </h1>
                <p class="mt-6 text-xl text-gray-600 max-w-2xl mx-auto lg:mx-0">
                    TraceX helps you track your expenses, manage budgets, and save smarter all in one app.
                </p>
            </div>
            <!-- right visual mockup -->
            <div class="flex-1 w-full relative">
                <div class="bg-white rounded-3xl shadow-2xl border border-gray-200 p-4 md:p-6">
                    <div class="flex items-center gap-2 mb-5">
                        <div class="w-3 h-3 rounded-full bg-rose-400"></div>
                        <div class="w-3 h-3 rounded-full bg-amber-400"></div>
                        <div class="w-3 h-3 rounded-full bg-emerald-400"></div>
                        <span class="ml-2 text-xs text-gray-400 font-medium">TraceX dashboard · preview</span>
                    </div>
                    <div class="flex flex-col md:flex-row gap-4 items-center">
                        <!-- laptop mock -->
                        <div class="bg-gray-100 rounded-xl p-3 w-full max-w-[280px] shadow-inner border border-gray-200">
                            <div class="bg-white rounded-lg h-40 w-full p-3 flex flex-col justify-between">
                                <div class="flex justify-between items-center">
                                    <span class="text-xs font-semibold text-indigo-600">May 2025</span>
                                    <span class="text-[10px] bg-indigo-100 px-2 py-0.5 rounded-full">+4.2%</span>
                                </div>
                                <!-- mini spending bars (graph) -->
                                <div class="space-y-2 mt-3">
                                    <div class="flex items-center gap-2"><span class="text-xs w-12">Food</span> <div class="h-2 flex-1 rounded-full bg-indigo-200"><div class="h-2 w-3/4 rounded-full bg-indigo-500"></div></div></div>
                                    <div class="flex items-center gap-2"><span class="text-xs w-12">Transport</span> <div class="h-2 flex-1 rounded-full bg-indigo-200"><div class="h-2 w-2/5 rounded-full bg-indigo-500"></div></div></div>
                                    <div class="flex items-center gap-2"><span class="text-xs w-12">Shopping</span> <div class="h-2 flex-1 rounded-full bg-indigo-200"><div class="h-2 w-4/5 rounded-full bg-indigo-500"></div></div></div>
                                </div>
                                <div class="flex justify-end mt-2"><div class="w-6 h-6 rounded-full border-2 border-indigo-400 border-r-transparent"></div></div>
                            </div>
                            <div class="mt-2 text-center text-[10px] text-gray-400">laptop view</div>
                        </div>
                        <!-- phone mock -->
                        <div class="bg-gray-100 rounded-2xl p-2 w-[120px] shadow-inner border border-gray-200">
                            <div class="bg-white rounded-lg h-32 w-full p-2 flex flex-col">
                                <div class="flex justify-between"><span class="text-[8px] font-bold">$342</span><span class="text-[6px] bg-emerald-100 px-1 rounded">budget</span></div>
                                <!-- tiny line chart -->
                                <svg viewBox="0 0 50 30" class="mt-1 w-full h-8 stroke-indigo-500 stroke-1 fill-none">
                                    <polyline points="2,20 12,10 22,18 32,5 42,12 48,20" stroke="currentColor" stroke-width="1.5" />
                                </svg>
                                <span class="mt-auto text-[6px] text-gray-400">phone sync</span>
                            </div>
                        </div>
                    </div>
                    <!-- spending trend -->
                    <div class="mt-6 border-t border-gray-100 pt-4 flex items-center justify-between text-xs text-gray-500">
                        <span><i class="fa-regular fa-clock mr-1"></i> spending trend</span>
                        <span class="text-emerald-600 font-medium">↓ 8% vs last month</span>
                    </div>
                </div>
                <!-- floating graph element (decor) -->
                <div class="absolute -bottom-4 -left-4 w-20 h-20 bg-indigo-100 rounded-full -z-10 blur-xl opacity-60"></div>
            </div>
        </div>
    </section>

    <!-- features section -->
    <section class="max-w-7xl mx-auto px-5 py-20 md:py-24">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900">Designed to give you clarity</h2>
            <p class="text-gray-600 mt-2 text-xl">Everything you need to master your money in one place.</p>
        </div>
        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- card 1 -->
            <div class="group feature-card relative overflow-hidden rounded-3xl border border-indigo-100 bg-white p-7 shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-xl">
                <div class="absolute right-0 top-0 h-24 w-24 rounded-bl-[3rem] bg-indigo-50 transition duration-300 group-hover:bg-indigo-100"></div>
                <div class="relative">
                    <div class="mb-6 flex items-center justify-between">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-indigo-600 text-xl text-white shadow-lg shadow-indigo-200">
                            <i class="fa-regular fa-compass"></i>
                        </div>
                        <span class="rounded-full bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-700">Live view</span>
                    </div>
                    <h3 class="text-xl font-bold leading-snug text-gray-900">Track Spending Effortlessly</h3>
                    <p class="mt-3 text-sm leading-7 text-gray-600">See where your money goes as you add expenses, with categories that keep each transaction easy to understand.</p>
                    <div class="mt-6 rounded-2xl bg-gray-50 p-4">
                        <div class="flex items-center justify-between text-xs text-gray-500">
                            <span>Today</span>
                            <span class="font-semibold text-indigo-700">12 entries</span>
                        </div>
                        <div class="mt-3 h-2 rounded-full bg-indigo-100">
                            <div class="h-2 w-3/4 rounded-full bg-indigo-500"></div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- card 2 -->
            <div class="group feature-card relative overflow-hidden rounded-3xl border border-emerald-100 bg-white p-7 shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-xl">
                <div class="absolute right-0 top-0 h-24 w-24 rounded-bl-[3rem] bg-emerald-50 transition duration-300 group-hover:bg-emerald-100"></div>
                <div class="relative">
                    <div class="mb-6 flex items-center justify-between">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-600 text-xl text-white shadow-lg shadow-emerald-200">
                            <i class="fa-regular fa-bell"></i>
                        </div>
                        <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">On track</span>
                    </div>
                    <h3 class="text-xl font-bold leading-snug text-gray-900">Smart Budgets</h3>
                    <p class="mt-3 text-sm leading-7 text-gray-600">Set monthly limits by category and notice overspending early, while there is still time to adjust.</p>
                    <div class="mt-6 grid grid-cols-3 gap-2 text-center">
                        <div class="rounded-2xl bg-emerald-50 px-2 py-3">
                            <p class="text-sm font-bold text-emerald-700">72%</p>
                            <p class="mt-1 text-[10px] font-medium text-gray-500">Used</p>
                        </div>
                        <div class="rounded-2xl bg-gray-50 px-2 py-3">
                            <p class="text-sm font-bold text-gray-800">8</p>
                            <p class="mt-1 text-[10px] font-medium text-gray-500">Days</p>
                        </div>
                        <div class="rounded-2xl bg-gray-50 px-2 py-3">
                            <p class="text-sm font-bold text-gray-800">3</p>
                            <p class="mt-1 text-[10px] font-medium text-gray-500">Budgets</p>
                        </div>
                    </div>
                </div>
            </div>
            <!-- card 3 -->
            <div class="group feature-card relative overflow-hidden rounded-3xl border border-amber-100 bg-white p-7 shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-xl">
                <div class="absolute right-0 top-0 h-24 w-24 rounded-bl-[3rem] bg-amber-50 transition duration-300 group-hover:bg-amber-100"></div>
                <div class="relative">
                    <div class="mb-6 flex items-center justify-between">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-500 text-xl text-white shadow-lg shadow-amber-200">
                            <i class="fa-regular fa-chart-bar"></i>
                        </div>
                        <span class="rounded-full bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-700">Patterns</span>
                    </div>
                    <h3 class="text-xl font-bold leading-snug text-gray-900">Visual Insights</h3>
                    <p class="mt-3 text-sm leading-7 text-gray-600">Use charts and summaries to spot spending trends, category changes, and months that need attention.</p>
                    <div class="mt-6 flex h-24 items-end gap-2 rounded-2xl bg-gray-50 p-4">
                        <div class="h-8 flex-1 rounded-t-lg bg-amber-200"></div>
                        <div class="h-14 flex-1 rounded-t-lg bg-amber-300"></div>
                        <div class="h-10 flex-1 rounded-t-lg bg-amber-200"></div>
                        <div class="h-20 flex-1 rounded-t-lg bg-amber-500"></div>
                        <div class="h-12 flex-1 rounded-t-lg bg-amber-300"></div>
                    </div>
                </div>
            </div>
            <!-- card 4 -->
            <div class="group feature-card relative overflow-hidden rounded-3xl border border-rose-100 bg-white p-7 shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-xl">
                <div class="absolute right-0 top-0 h-24 w-24 rounded-bl-[3rem] bg-rose-50 transition duration-300 group-hover:bg-rose-100"></div>
                <div class="relative">
                    <div class="mb-6 flex items-center justify-between">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-rose-500 text-xl text-white shadow-lg shadow-rose-200">
                            <i class="fa-solid fa-lock"></i>
                        </div>
                        <span class="rounded-full bg-rose-50 px-3 py-1 text-xs font-semibold text-rose-700">Private</span>
                    </div>
                    <h3 class="text-xl font-bold leading-snug text-gray-900">Secure & Private</h3>
                    <p class="mt-3 text-sm leading-7 text-gray-600">Your financial records stay focused on you, with account-based access and no public sharing.</p>
                    <div class="mt-6 rounded-2xl border border-rose-100 bg-rose-50 p-4">
                        <div class="flex items-center gap-3">
                            <div class="flex h-9 w-9 items-center justify-center rounded-full bg-white text-rose-500">
                                <i class="fa-solid fa-shield-halved"></i>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-gray-900">Protected records</p>
                                <p class="text-xs text-gray-500">Login required</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- money habits section -->
    <section class="max-w-7xl mx-auto px-5 py-20 md:py-24">
        <div class="grid lg:grid-cols-[0.9fr_1.1fr] gap-10 lg:gap-14 items-start">
            <div class="lg:sticky lg:top-28">
                <span class="inline-flex items-center gap-2 rounded-full border border-emerald-200 bg-emerald-50 px-4 py-2 text-sm font-semibold text-emerald-700">
                    <i class="fa-solid fa-seedling"></i>
                    Smart money habits
                </span>
                <h2 class="mt-5 text-3xl md:text-4xl font-bold leading-tight text-gray-900">
                    Saving feels easier when your expenses finally make sense.
                </h2>
                <p class="mt-4 text-lg leading-8 text-gray-600">
                    TraceX turns everyday spending into simple patterns you can act on. Learn what to keep, what to trim, and how to build a money routine that lasts.
                </p>
                <div class="mt-8 grid grid-cols-3 gap-3 rounded-3xl border border-gray-100 bg-white p-3 shadow-sm">
                    <div class="rounded-2xl bg-indigo-50 px-4 py-5 text-center">
                        <p class="text-2xl font-bold text-indigo-700">50</p>
                        <p class="mt-1 text-xs font-medium text-gray-500">Needs</p>
                    </div>
                    <div class="rounded-2xl bg-amber-50 px-4 py-5 text-center">
                        <p class="text-2xl font-bold text-amber-700">30</p>
                        <p class="mt-1 text-xs font-medium text-gray-500">Wants</p>
                    </div>
                    <div class="rounded-2xl bg-emerald-50 px-4 py-5 text-center">
                        <p class="text-2xl font-bold text-emerald-700">20</p>
                        <p class="mt-1 text-xs font-medium text-gray-500">Savings</p>
                    </div>
                </div>
            </div>

            <div class="grid gap-6">
                <article class="overflow-hidden rounded-3xl border border-gray-100 bg-white shadow-lg md:grid md:grid-cols-[0.9fr_1.1fr]">
                    <div class="relative min-h-64 md:min-h-full">
                        <img src="https://images.unsplash.com/photo-1554224155-6726b3ff858f?auto=format&fit=crop&w=900&q=80" alt="Budget planning notebook and calculator" class="absolute inset-0 h-full w-full object-cover">
                        <div class="absolute left-4 top-4 rounded-full bg-white/90 px-3 py-1 text-xs font-semibold text-gray-700 shadow-sm">Budget rule</div>
                    </div>
                    <div class="p-7 md:p-8">
                        <div class="flex flex-wrap gap-2">
                            <span class="rounded-full bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-700">50/30/20 Rule</span>
                            <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-600">Beginner friendly</span>
                        </div>
                        <h3 class="mt-5 text-2xl font-bold text-gray-900">Give every kyat a clear role.</h3>
                        <p class="mt-3 text-gray-600 leading-7">
                            Split your income into essentials, lifestyle spending, and future savings. When your expenses are tagged, the rule becomes visible instead of theoretical.
                        </p>
                        <div class="mt-6 space-y-3 text-sm text-gray-600">
                            <div class="flex items-center gap-3"><i class="fa-solid fa-circle-check text-emerald-500"></i> Spot lifestyle spending before it quietly grows</div>
                            <div class="flex items-center gap-3"><i class="fa-solid fa-circle-check text-emerald-500"></i> Keep rent, food, transport, and bills in context</div>
                        </div>
                    </div>
                </article>

                <div class="grid md:grid-cols-2 gap-6">
                    <article class="overflow-hidden rounded-3xl border border-gray-100 bg-white shadow-md">
                        <div class="relative h-52">
                            <img src="https://images.unsplash.com/photo-1579621970563-ebec7560ff3e?auto=format&fit=crop&w=800&q=80" alt="Coins stacked with financial notes" class="h-full w-full object-cover">
                            <span class="absolute left-4 top-4 rounded-full bg-white/90 px-3 py-1 text-xs font-semibold text-emerald-700 shadow-sm">Saving system</span>
                        </div>
                        <div class="p-7">
                            <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">Pay Yourself First</span>
                            <h3 class="mt-5 text-xl font-bold text-gray-900">Move saving before spending.</h3>
                            <p class="mt-3 text-gray-600 leading-7">Treat savings like a monthly bill to your future self, then track what remains for daily decisions.</p>
                        </div>
                    </article>

                    <article class="overflow-hidden rounded-3xl border border-gray-100 bg-white shadow-md">
                        <div class="relative h-52">
                            <img src="https://images.unsplash.com/photo-1554224154-22dec7ec8818?auto=format&fit=crop&w=800&q=80" alt="Person reviewing expense receipts" class="h-full w-full object-cover">
                            <span class="absolute left-4 top-4 rounded-full bg-white/90 px-3 py-1 text-xs font-semibold text-amber-700 shadow-sm">Expense clarity</span>
                        </div>
                        <div class="p-7">
                            <span class="rounded-full bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-700">Know Your Tags</span>
                            <h3 class="mt-5 text-xl font-bold text-gray-900">Small labels reveal big habits.</h3>
                            <p class="mt-3 text-gray-600 leading-7">Tags turn scattered receipts into patterns, making it easier to adjust before the month gets tight.</p>
                        </div>
                    </article>
                </div>
            </div>
        </div>
    </section>

    <!-- how it works --->
    <section class="max-w-5xl mx-auto px-5 py-16 md:py-20 rounded-3xl md:rounded-4xl">
        <div class="text-center mb-14">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900">Start in minutes</h2>
            <p class="text-gray-600 mt-2 text-lg">No complicated setup, just you and your goals.</p>
        </div>
        <div class="relative max-w-3xl mx-auto">
            <div class="absolute left-8 top-8 bottom-8 w-0.5 rounded-full bg-indigo-200 md:left-1/2 md:-translate-x-1/2"></div>

            <div class="relative grid gap-8">
                <div class="relative md:grid md:grid-cols-2 md:gap-12 md:items-center">
                    <div class="md:text-right md:pr-8">
                        <div class="ml-20 rounded-3xl border border-white bg-white p-6 shadow-sm md:ml-0">
                            <span class="text-sm font-semibold text-indigo-600">Step 01</span>
                            <h3 class="text-xl font-semibold mt-2 text-gray-900">Sign Up</h3>
                            <p class="text-gray-600 mt-2">Create your account in seconds.</p>
                        </div>
                    </div>
                    <div class="absolute left-0 top-5 step-circle w-16 h-16 bg-indigo-600 text-white rounded-3xl text-2xl font-bold flex items-center justify-center shadow-lg shadow-indigo-200 md:left-1/2 md:-translate-x-1/2">1</div>
                    <div class="hidden md:block"></div>
                </div>

                <div class="relative md:grid md:grid-cols-2 md:gap-12 md:items-center">
                    <div class="hidden md:block"></div>
                    <div class="md:pl-8">
                        <div class="ml-20 rounded-3xl border border-white bg-white p-6 shadow-sm md:ml-0">
                            <span class="text-sm font-semibold text-indigo-600">Step 02</span>
                            <h3 class="text-xl font-semibold mt-2 text-gray-900">Save Expenses</h3>
                            <p class="text-gray-600 mt-2">Start saving your expenses, you're in control.</p>
                        </div>
                    </div>
                    <div class="absolute left-0 top-5 step-circle w-16 h-16 bg-indigo-600 text-white rounded-3xl text-2xl font-bold flex items-center justify-center shadow-lg shadow-indigo-200 md:left-1/2 md:-translate-x-1/2">2</div>
                </div>

                <div class="relative md:grid md:grid-cols-2 md:gap-12 md:items-center">
                    <div class="md:text-right md:pr-8">
                        <div class="ml-20 rounded-3xl border border-white bg-white p-6 shadow-sm md:ml-0">
                            <span class="text-sm font-semibold text-indigo-600">Step 03</span>
                            <h3 class="text-xl font-semibold mt-2 text-gray-900">Track & Optimize</h3>
                            <p class="text-gray-600 mt-2">Analyze, plan, and save smarter with real insights.</p>
                        </div>
                    </div>
                    <div class="absolute left-0 top-5 step-circle w-16 h-16 bg-indigo-600 text-white rounded-3xl text-2xl font-bold flex items-center justify-center shadow-lg shadow-indigo-200 md:left-1/2 md:-translate-x-1/2">3</div>
                    <div class="hidden md:block"></div>
                </div>

                <div class="relative md:grid md:grid-cols-2 md:gap-12 md:items-center">
                    <div class="hidden md:block"></div>
                    <div class="md:pl-8">
                        <div class="ml-20 rounded-3xl border border-white bg-white p-6 shadow-sm md:ml-0">
                            <span class="text-sm font-semibold text-indigo-600">Step 04</span>
                            <h3 class="text-xl font-semibold mt-2 text-gray-900">View Insights</h3>
                            <p class="text-gray-600 mt-2">Review spending patterns, top expenses, and category insights to make better money decisions.</p>
                        </div>
                    </div>
                    <div class="absolute left-0 top-5 step-circle w-16 h-16 bg-indigo-600 text-white rounded-3xl text-2xl font-bold flex items-center justify-center shadow-lg shadow-indigo-200 md:left-1/2 md:-translate-x-1/2">4</div>
                </div>
            </div>
        </div>
    </section>

    <!-- testimonials -->
    <!-- <section class="max-w-7xl mx-auto px-5 py-20 md:py-24">
        <div class="text-center mb-14">
            <span class="text-sm font-semibold text-indigo-600 uppercase tracking-wider">Trusted by early adopters</span>
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mt-3">Loved by finance enthusiasts</h2>
        </div>
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            <div class="bg-white p-8 rounded-3xl border border-gray-100 shadow-sm">
                <div class="flex text-amber-400 text-lg gap-0.5 mb-4">★★★★★</div>
                <p class="text-gray-700 text-lg italic">“TraceX turned my chaotic expenses into crystal clear categories. The alerts saved me from overdrafts twice!”</p>
                <div class="mt-6 flex items-center gap-3">
                    <div class="w-11 h-11 bg-indigo-100 rounded-full flex items-center justify-center text-indigo-700 font-bold">MP</div>
                    <div><p class="font-semibold text-gray-900">Megan Park</p><p class="text-sm text-gray-500">early adopter</p></div>
                </div>
            </div>
        </div>
        <p class="text-center text-gray-500 text-sm mt-12"><i class="fa-regular fa-star mr-1"></i> Join 2,000+ users who started their journey</p>
    </section> -->

    <!-- footer -->
    <footer class="mt-20 border-t border-gray-200 bg-gray-100">
        <div class="max-w-7xl mx-auto px-5 py-14 md:py-20">
            <div class="grid gap-10 md:grid-cols-[1.2fr_0.8fr_0.6fr] md:items-start">
                <div>
                    <div class="flex items-center gap-3">
                        <!-- <img src="/public/assets/test.png" alt="TraceX Logo" class="h-11 rounded-xl"> -->
                        <span class="text-3xl font-bold text-indigo-600">TraceX</span>
                    </div>
                    <p class="mt-5 max-w-xl text-base leading-8 text-gray-600">
                        Track expenses, understand your spending habits, and make clearer money decisions with a simple personal finance dashboard.
                    </p>
                </div>

                <div>
                    <h3 class="text-sm font-bold uppercase tracking-wider text-gray-900">Useful links</h3>
                    <div class="mt-5 flex flex-col gap-3 text-base font-medium text-gray-600">
                        <a href="<?= url('terms-and-conditions.php') ?>" class="hover:text-indigo-600 transition">Terms & Conditions</a>
                        <a href="https://aungkyawthet-portfolio.vercel.app/#contact" target="_blank" class="hover:text-indigo-600 transition">Contact</a>
                    </div>
                </div>

                <div>
                    <h3 class="text-sm font-bold uppercase tracking-wider text-gray-900">Follow</h3>
                    <div class="mt-5 flex gap-4 text-2xl text-gray-600">
                        <a href="https://github.com/aungkyawthetx" target="_blank" class="flex h-11 w-11 items-center justify-center rounded-full bg-white shadow-sm transition hover:-translate-y-0.5 hover:text-indigo-600 hover:shadow-md" aria-label="GitHub">
                            <i class="fab fa-github"></i>
                        </a>
                    </div>
                </div>
            </div>

            <div class="mt-12 flex flex-col gap-3 border-t border-gray-300 pt-6 text-base text-gray-500 md:flex-row md:items-center md:justify-between">
                <p>&copy; TraceX. Made with <i class="fa-regular fa-heart text-rose-500"></i> for smart spenders.</p>
                <p class="font-medium text-gray-600">Smart expense tracking for everyday decisions.</p>
            </div>
        </div>
    </footer>
    <script>
        const mobileMenuBtn = document.getElementById('mobile-menu-btn');
        const mobileMenu = document.getElementById('mobile-menu');
        const mobileMenuIcon = document.getElementById('mobile-menu-icon');

        function closeMobileMenu() {
            mobileMenu.classList.add('max-h-0', 'opacity-0');
            mobileMenu.classList.remove('max-h-48', 'opacity-100');
            mobileMenuBtn.setAttribute('aria-expanded', 'false');
            mobileMenuIcon.classList.remove('fa-xmark');
            mobileMenuIcon.classList.add('fa-bars');
        }

        function openMobileMenu() {
            mobileMenu.classList.remove('max-h-0', 'opacity-0');
            mobileMenu.classList.add('max-h-48', 'opacity-100');
            mobileMenuBtn.setAttribute('aria-expanded', 'true');
            mobileMenuIcon.classList.remove('fa-bars');
            mobileMenuIcon.classList.add('fa-xmark');
        }

        mobileMenuBtn.addEventListener('click', function () {
            if (mobileMenuBtn.getAttribute('aria-expanded') === 'true') {
                closeMobileMenu();
            } else {
                openMobileMenu();
            }
        });

        document.addEventListener('click', function (event) {
            if (!mobileMenu.contains(event.target) && !mobileMenuBtn.contains(event.target)) {
                closeMobileMenu();
            }
        });

        window.addEventListener('resize', function () {
            if (window.innerWidth >= 768) {
                closeMobileMenu();
            }
        });
    </script>
</body>
</html>
