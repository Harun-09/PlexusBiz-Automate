import { Head, Link, router, useForm } from '@inertiajs/react';
import { useMemo, useState, Fragment } from 'react';
import { Dialog, Transition } from '@headlessui/react';
import FrontendLayout from '@/Layouts/FrontendLayout';
import FlashBanner from '@/Components/FlashBanner';

const fallbackImage = '/images/landing/deal-imac.jpg';

function formatMoney(amount, currency = 'BDT') {
    const numericAmount = Number(amount ?? 0);
    try {
        return new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: currency || 'BDT',
            maximumFractionDigits: 2,
        }).format(numericAmount);
    } catch {
        return `${currency || 'BDT'} ${numericAmount.toFixed(2)}`;
    }
}

function resolveTierPrice(quantity, tiers, fallbackPrice) {
    const sortedTiers = Array.isArray(tiers)
        ? [...tiers].sort((left, right) => Number(left.min_quantity) - Number(right.min_quantity))
        : [];
    const tier = sortedTiers.filter((entry) => Number(quantity) >= Number(entry.min_quantity)).pop();
    return { tier, price: Number(tier?.unit_price ?? fallbackPrice ?? 0) };
}

function SuggestionCard({ product, currency }) {
    const inStock = Number(product.available_stock ?? 0) > 0;
    return (
        <article className="group overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm transition hover:shadow-md">
            <Link href={route('products.show', product.slug || 'slug')} className="block relative aspect-square bg-slate-50 overflow-hidden">
                <img
                    src={product.primary_image_url || fallbackImage}
                    alt={product.name}
                    className="h-full w-full object-contain p-4 mix-blend-multiply transition-transform duration-300 group-hover:scale-105"
                    onError={(e) => { e.currentTarget.src = fallbackImage; }}
                />
            </Link>
            <div className="p-4 flex flex-col justify-between h-[140px]">
                <div>
                    <p className="text-[10px] font-bold uppercase tracking-wider text-slate-400 truncate">
                        {product.supplier?.company_name || 'Supplier'}
                    </p>
                    <h3 className="mt-1 text-sm font-semibold text-slate-800 line-clamp-2">
                        <Link href={route('products.show', product.slug || 'slug')} className="transition hover:text-blue-600">
                            {product.name}
                        </Link>
                    </h3>
                </div>
                <div className="mt-3 flex items-center justify-between">
                    <div>
                        <p className="text-xs text-slate-500">From</p>
                        <p className="text-base font-bold text-blue-700">{formatMoney(product.base_price, currency)}</p>
                    </div>
                    <button
                        type="button"
                        disabled={!inStock}
                        onClick={() => {
                            if (!inStock) return;
                            router.post(route('cart.add'), { product_id: product.id, quantity: Number(product.moq || 1) }, { preserveScroll: true });
                        }}
                        className="flex h-8 w-8 items-center justify-center rounded-full bg-blue-50 text-blue-600 transition hover:bg-blue-600 hover:text-white disabled:cursor-not-allowed disabled:opacity-50"
                    >
                        <svg className="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </button>
                </div>
            </div>
        </article>
    );
}

function TierTable({ tiers, quantity, currency, basePrice, onSelectTier }) {
    const rows = Array.isArray(tiers) ? [...tiers].sort((left, right) => left.min_quantity - right.min_quantity) : [];
    const current = resolveTierPrice(quantity, rows, basePrice);
    if (rows.length === 0) return null;
    return (
        <div className="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden transition-all">
            <div className="border-b border-slate-200 px-5 py-3 bg-slate-50">
                <h3 className="text-sm font-semibold text-slate-800 flex items-center justify-between">
                    Volume Pricing
                    <span className="text-[10px] font-normal text-slate-500 bg-slate-200/50 px-2 py-0.5 rounded-full">Tap to apply</span>
                </h3>
            </div>
            <div className="divide-y divide-slate-100">
                {rows.map((tier) => {
                    const active = current.tier?.id === tier.id;
                    return (
                        <div 
                            key={tier.id} 
                            onClick={() => onSelectTier && onSelectTier(Number(tier.min_quantity))}
                            className={`flex items-center justify-between px-5 py-3 cursor-pointer transition-all duration-200 ${active ? 'bg-blue-50' : 'hover:bg-slate-50 hover:pl-6'}`}
                        >
                            <div className="flex items-center gap-2">
                                <span className={`text-sm ${active ? 'font-bold text-blue-700' : 'font-medium text-slate-700'}`}>
                                    {Number(tier.min_quantity)}+ units
                                </span>
                                {active && <span className="rounded shadow-sm bg-blue-600 px-2 py-0.5 text-[10px] font-bold text-white uppercase tracking-wide">Active</span>}
                            </div>
                            <div className="text-right">
                                <p className={`text-sm ${active ? 'font-bold text-blue-700' : 'font-semibold text-slate-800'}`}>
                                    {formatMoney(tier.unit_price, currency)} <span className={`text-xs ${active ? 'text-blue-500' : 'text-slate-500'}`}>/ unit</span>
                                </p>
                            </div>
                        </div>
                    );
                })}
            </div>
        </div>
    );
}

export default function Show({ auth, flash, errors, cartCount, currency, defaultQuantity, product, relatedProducts, supplierProducts, isPurchasable }) {
    const gallery = Array.isArray(product.gallery) && product.gallery.length > 0
        ? product.gallery
        : [{ url: product.primary_image_url || fallbackImage, alt: product.name, is_primary: true }];

    const [selectedImage, setSelectedImage] = useState(gallery[0]?.url || product.primary_image_url || fallbackImage);
    const [quantity, setQuantity] = useState(Math.max(1, Number(defaultQuantity || product.moq || 1)));
    const [activeTab, setActiveTab] = useState('overview');
    
    // AI Assistant State
    const [aiQuery, setAiQuery] = useState('');
    const [aiResponse, setAiResponse] = useState('');
    const [isThinking, setIsThinking] = useState(false);

    const validationMessage = Object.values(errors || {}).find(Boolean);

    const isB2B = !!auth?.user;
    
    const [isRfqModalOpen, setIsRfqModalOpen] = useState(false);
    const rfqForm = useForm({
        contact_name: auth?.user?.name || '',
        company_name: auth?.user?.company_name || auth?.user?.name || '',
        email: auth?.user?.email || '',
        product_id: product.id,
        product_name: product.name,
        quantity: quantity,
        target_price: '',
        message: `I would like to request a quote for this product.`,
    });

    const submitRfq = (e) => {
        e.preventDefault();
        rfqForm.post(route('rfq.store'), {
            preserveScroll: true,
            onSuccess: () => setIsRfqModalOpen(false),
        });
    };

    const availableStock = Number(product.available_stock ?? 0);
    const minimumOrder = isB2B ? Number(product.moq ?? 1) : 1;
    const canPurchase = Boolean(isPurchasable) && availableStock >= minimumOrder;
    const safeQuantity = useMemo(() => {
        if (!canPurchase) return Math.max(1, minimumOrder);
        return Math.min(Math.max(1, quantity), Math.max(minimumOrder, availableStock));
    }, [availableStock, canPurchase, minimumOrder, quantity]);

    const pricing = useMemo(
        () => resolveTierPrice(safeQuantity, isB2B ? (product.pricing_tiers || []) : [], Number(product.base_price ?? 0)),
        [product.pricing_tiers, product.base_price, safeQuantity, isB2B],
    );
    
    const maxTierQuantity = isB2B && Array.isArray(product.pricing_tiers) && product.pricing_tiers.length > 0 
        ? Math.max(...product.pricing_tiers.map(t => Number(t.min_quantity)))
        : minimumOrder;
    
    const sliderMax = Math.max(minimumOrder, Math.min(availableStock, Math.max(minimumOrder * 2, Math.ceil(maxTierQuantity * 1.5))));

    const unitPrice = pricing.price;
    const lineTotal = unitPrice * safeQuantity;
    const savings = Number(product.base_price ?? 0) - unitPrice;
    
    let stockStatusText = 'Out of stock';
    let stockStatusColor = 'bg-red-100 text-red-700';
    if (canPurchase) {
        if (availableStock <= minimumOrder + 10) {
            stockStatusText = `Only ${availableStock} left`;
            stockStatusColor = 'bg-orange-100 text-orange-800';
        } else {
            stockStatusText = `In stock`;
            stockStatusColor = 'bg-green-100 text-green-800';
        }
    }

    const addToCart = () => {
        if (!canPurchase) return;
        router.post(route('cart.add'), { product_id: product.id, quantity: safeQuantity }, { preserveScroll: true });
    };

    const buyNow = () => {
        if (!canPurchase) return;
        router.post(route('cart.add'), { product_id: product.id, quantity: safeQuantity }, {
            preserveScroll: true,
            onSuccess: () => router.visit(route('checkout.index')),
        });
    };

    const askAi = (question) => {
        if (!question) return;
        setAiQuery(question);
        setIsThinking(true);
        setAiResponse('');
        // Dummy timeout to simulate AI response
        setTimeout(() => {
            setIsThinking(false);
            setAiResponse(`Based on the product details, ${product.name} is a highly rated item in the ${product.category?.name || 'catalog'}. It has a minimum order quantity of ${minimumOrder} units and is currently ${canPurchase ? 'in stock' : 'out of stock'}.`);
        }, 1500);
    };

    // Dummy UI Data
    const highlights = [
        "Premium quality material and build",
        "Fast delivery options available",
        "Secure and encrypted checkout",
        "Trusted verified supplier",
        "100% genuine product guarantee"
    ];

    const specs = [
        ['Brand', product.brand?.name || 'Plexus Generic'],
        ['Category', product.category?.name || 'N/A'],
        ['SKU', product.sku || 'N/A'],
        ['Weight', '1.2 kg (Approx)'],
        ['Dimensions', '20 x 15 x 10 cm'],
        ['Total Sold', `${Math.floor(Math.random() * 500) + 50} Units`],
        ['Condition', 'Brand New']
    ];

    const aiQuestions = [
        "What are the key features?",
        "Is there a warranty?",
        "How fast is shipping?"
    ];

    return (
        <FrontendLayout auth={auth} canLogin={true} cartCount={cartCount}>
            <Head title={product.name} />

            <div className="min-h-screen bg-slate-50 pb-16 pt-8 font-sans">
                <main className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    {/* Breadcrumbs */}
                    <nav className="mb-6 flex items-center text-sm font-medium text-slate-500">
                        <Link href="/" className="hover:text-blue-600 transition">Home</Link>
                        <svg className="mx-2 h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M9 5l7 7-7 7"></path></svg>
                        <Link href={route('products.index')} className="hover:text-blue-600 transition">Products</Link>
                        <svg className="mx-2 h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M9 5l7 7-7 7"></path></svg>
                        <span className="text-slate-900 font-semibold truncate">{product.category?.name || 'Category'}</span>
                    </nav>

                    <FlashBanner message={flash?.success} />
                    <FlashBanner message={flash?.error} type="error" />
                    <FlashBanner message={validationMessage} type="error" />

                    <div className="grid grid-cols-1 gap-8 lg:grid-cols-12">
                        {/* Main Product Area */}
                        <div className="lg:col-span-8 space-y-8">
                            
                            {/* Product Header & Gallery */}
                            <div className="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                                <div className="grid grid-cols-1 gap-8 md:grid-cols-2">
                                    {/* Gallery */}
                                    <div className="flex flex-col gap-4">
                                        <div className="relative overflow-hidden rounded-xl border border-slate-100 bg-slate-50 aspect-square flex items-center justify-center p-4">
                                            <span className={`absolute left-4 top-4 z-10 rounded-md px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider ${stockStatusColor}`}>
                                                {stockStatusText}
                                            </span>
                                            <img
                                                src={selectedImage}
                                                alt={product.name}
                                                className="max-h-full max-w-full object-contain mix-blend-multiply"
                                                onError={(e) => { e.currentTarget.src = fallbackImage; }}
                                            />
                                        </div>
                                        <div className="grid grid-cols-5 gap-2">
                                            {gallery.map((img, idx) => (
                                                <button
                                                    key={idx}
                                                    type="button"
                                                    onClick={() => setSelectedImage(img.url)}
                                                    className={`overflow-hidden rounded-lg border-2 transition ${selectedImage === img.url ? 'border-blue-500' : 'border-transparent hover:border-slate-300'} bg-slate-50 aspect-square`}
                                                >
                                                    <img src={img.url} alt={img.alt} className="h-full w-full object-cover mix-blend-multiply" onError={(e) => { e.currentTarget.src = fallbackImage; }}/>
                                                </button>
                                            ))}
                                        </div>
                                    </div>

                                    {/* Core Info */}
                                    <div className="flex flex-col">
                                        <div className="flex items-center gap-3 text-sm font-semibold text-blue-600">
                                            <Link href="#" className="hover:underline">
                                                Sold by {product.supplier?.company_name || 'Verified Supplier'}
                                            </Link>
                                            <span className="h-3 w-px bg-slate-300"></span>
                                            <button className="text-slate-500 hover:text-blue-600 transition">Follow Store</button>
                                        </div>
                                        <h1 className="mt-2 text-2xl font-bold text-slate-900 tracking-tight leading-snug">
                                            {product.name}
                                        </h1>
                                        
                                        {/* Meta row: Rating */}
                                        <div className="mt-3 flex items-center gap-3">
                                            <div className="flex text-amber-400 text-sm">
                                                <span>★</span><span>★</span><span>★</span><span>★</span><span className="text-slate-300">★</span>
                                            </div>
                                            <span className="text-sm font-semibold text-slate-800">4.2</span>
                                            <Link href="#" className="text-sm text-blue-600 hover:underline">128 reviews</Link>
                                            <span className="h-4 w-px bg-slate-200"></span>
                                            <span className="text-sm text-slate-600">42 answered questions</span>
                                        </div>

                                        <div className="mt-4 flex items-center gap-4">
                                            <div className="flex flex-col">
                                                <span className="text-xs font-medium text-slate-500">SKU</span>
                                                <span className="text-sm font-semibold text-slate-800">{product.sku || 'N/A'}</span>
                                            </div>
                                            <div className="h-8 w-px bg-slate-200"></div>
                                            <div className="flex flex-col">
                                                <span className="text-xs font-medium text-slate-500">MOQ</span>
                                                <span className="text-sm font-semibold text-slate-800">{minimumOrder} Units</span>
                                            </div>
                                        </div>

                                        {/* Promo Tags */}
                                        <div className="mt-5 flex flex-wrap gap-2">
                                            <span className="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">Secure Checkout</span>
                                            <span className="rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700">Fast Shipping</span>
                                            <span className="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">Authentic</span>
                                        </div>

                                        <p className="mt-6 text-sm text-slate-600 leading-relaxed">
                                            {product.description || 'No detailed description available for this product.'}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            {/* Features & AI Assistant */}
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-8">
                                {/* Highlights */}
                                <div className="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                                    <h2 className="text-lg font-bold text-slate-900 mb-4">Highlights</h2>
                                    <ul className="space-y-3">
                                        {highlights.map((h, i) => (
                                            <li key={i} className="flex items-start gap-3">
                                                <svg className="h-5 w-5 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M5 13l4 4L19 7"></path></svg>
                                                <span className="text-sm text-slate-700">{h}</span>
                                            </li>
                                        ))}
                                    </ul>
                                </div>

                                {/* AI Assistant */}
                                <div className="rounded-2xl border border-blue-100 bg-blue-50/50 p-6 shadow-sm">
                                    <div className="flex items-center gap-2 mb-4">
                                        <svg className="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                        <h2 className="text-lg font-bold text-slate-900">Ask AI Assistant</h2>
                                    </div>
                                    <div className="flex flex-wrap gap-2 mb-4">
                                        {aiQuestions.map((q, i) => (
                                            <button 
                                                key={i} 
                                                onClick={() => askAi(q)}
                                                className="rounded-full border border-blue-200 bg-white px-3 py-1.5 text-xs font-medium text-slate-600 hover:border-blue-400 hover:text-blue-700 transition"
                                            >
                                                {q}
                                            </button>
                                        ))}
                                    </div>
                                    <div className="flex gap-2">
                                        <input 
                                            type="text" 
                                            value={aiQuery}
                                            onChange={(e) => setAiQuery(e.target.value)}
                                            onKeyDown={(e) => e.key === 'Enter' && askAi(aiQuery)}
                                            placeholder="Ask something else..." 
                                            className="flex-1 rounded-lg border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        />
                                        <button onClick={() => askAi(aiQuery)} className="rounded-lg bg-blue-600 px-4 text-sm font-bold text-white shadow-sm hover:bg-blue-700 transition">Ask</button>
                                    </div>
                                    {isThinking && (
                                        <div className="mt-4 p-4 rounded-xl bg-white border border-blue-100 text-sm text-slate-500 flex items-center gap-2 italic">
                                            Thinking<span className="flex gap-1"><span className="animate-bounce">.</span><span className="animate-bounce" style={{animationDelay: '0.1s'}}>.</span><span className="animate-bounce" style={{animationDelay: '0.2s'}}>.</span></span>
                                        </div>
                                    )}
                                    {aiResponse && !isThinking && (
                                        <div className="mt-4 p-4 rounded-xl bg-white border border-blue-200 text-sm text-slate-700 leading-relaxed shadow-sm">
                                            <p className="font-bold text-blue-600 text-xs mb-1 uppercase tracking-wide">AI Answer</p>
                                            {aiResponse}
                                        </div>
                                    )}
                                </div>
                            </div>

                            {/* Specifications Grid */}
                            <div className="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                                <div className="border-b border-slate-200 px-6 py-4 bg-slate-50">
                                    <h2 className="text-base font-bold text-slate-900">Detailed Specifications</h2>
                                </div>
                                <div className="grid grid-cols-1 sm:grid-cols-2 divide-y sm:divide-y-0 sm:divide-x divide-slate-100">
                                    <div className="divide-y divide-slate-100">
                                        {specs.slice(0, 4).map((row, i) => (
                                            <div key={i} className="flex justify-between px-6 py-4 hover:bg-slate-50/50 transition">
                                                <span className="text-sm font-medium text-slate-500">{row[0]}</span>
                                                <span className="text-sm font-semibold text-slate-900 text-right">{row[1]}</span>
                                            </div>
                                        ))}
                                    </div>
                                    <div className="divide-y divide-slate-100 border-t sm:border-t-0 border-slate-100">
                                        {specs.slice(4).map((row, i) => (
                                            <div key={i} className="flex justify-between px-6 py-4 hover:bg-slate-50/50 transition">
                                                <span className="text-sm font-medium text-slate-500">{row[0]}</span>
                                                <span className="text-sm font-semibold text-slate-900 text-right">{row[1]}</span>
                                            </div>
                                        ))}
                                    </div>
                                </div>
                            </div>

                            {/* Dummy Compare Section */}
                            <div className="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                                <div className="border-b border-slate-200 px-6 py-4 bg-slate-50">
                                    <h2 className="text-base font-bold text-slate-900">Compare with Similar Products</h2>
                                </div>
                                <div className="overflow-x-auto">
                                    <table className="w-full text-left text-sm whitespace-nowrap">
                                        <thead className="bg-slate-50 border-b border-slate-200">
                                            <tr>
                                                <th className="px-6 py-4 font-semibold text-slate-700">Model</th>
                                                <th className="px-6 py-4 font-semibold text-slate-700">Price</th>
                                                <th className="px-6 py-4 font-semibold text-slate-700">Rating</th>
                                                <th className="px-6 py-4 font-semibold text-slate-700">Brand</th>
                                            </tr>
                                        </thead>
                                        <tbody className="divide-y divide-slate-100">
                                            <tr className="bg-blue-50/30">
                                                <td className="px-6 py-4 font-bold text-slate-900 flex items-center gap-3">
                                                    <img src={selectedImage} alt="" className="w-8 h-8 rounded border object-contain bg-white" />
                                                    {product.name.slice(0,25)}... (This Item)
                                                </td>
                                                <td className="px-6 py-4 font-semibold text-slate-900">{formatMoney(unitPrice, currency)}</td>
                                                <td className="px-6 py-4 font-medium text-slate-700 text-amber-500">★ 4.2</td>
                                                <td className="px-6 py-4 font-medium text-slate-700">{product.brand?.name || 'Plexus Generic'}</td>
                                            </tr>
                                            {relatedProducts && relatedProducts.slice(0, 3).map((item, idx) => (
                                                <tr key={idx} className="hover:bg-slate-50 transition">
                                                    <td className="px-6 py-4 font-medium text-blue-600 hover:underline flex items-center gap-3">
                                                        <img src={item.primary_image_url || fallbackImage} alt="" className="w-8 h-8 rounded border object-contain bg-white" />
                                                        <Link href={route('products.show', item.slug)}>{item.name.slice(0,25)}...</Link>
                                                    </td>
                                                    <td className="px-6 py-4 font-semibold text-slate-900">{formatMoney(item.base_price, currency)}</td>
                                                    <td className="px-6 py-4 font-medium text-slate-700 text-amber-500">★ {(4.0 + (idx * 0.1)).toFixed(1)}</td>
                                                    <td className="px-6 py-4 font-medium text-slate-700">{item.brand?.name || 'Other Brand'}</td>
                                                </tr>
                                            ))}
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        {/* Right Sidebar - Buy Box */}
                        <div className="lg:col-span-4 space-y-6">
                            <div className="sticky top-24 rounded-2xl border border-slate-200 bg-white shadow-lg shadow-slate-200/50 flex flex-col">
                                <div className="p-6 pb-0">
                                    <h3 className="text-sm font-semibold text-slate-500 uppercase tracking-wide">Order Summary</h3>
                                    {isB2B ? (
                                        <div className="mt-4 flex flex-col">
                                            {savings > 0 ? (
                                                <>
                                                    <span className="text-sm font-medium text-slate-500 line-through">Normal Price: {formatMoney(product.base_price, currency)}</span>
                                                    <div className="flex items-end gap-3 mt-1">
                                                        <span className="text-3xl font-bold text-blue-700">{formatMoney(unitPrice, currency)}</span>
                                                        <span className="text-sm font-medium text-blue-500 mb-1">/ unit (Your Bulk Price)</span>
                                                    </div>
                                                </>
                                            ) : (
                                                <div className="flex items-end gap-3 mt-1">
                                                    <span className="text-3xl font-bold text-slate-900">{formatMoney(unitPrice, currency)}</span>
                                                    <span className="text-sm font-medium text-slate-500 mb-1">/ unit</span>
                                                </div>
                                            )}
                                            {savings > 0 && (
                                                <div className="mt-2 self-start rounded bg-green-50 px-2 py-1 text-xs font-semibold text-green-700">
                                                    You save {formatMoney(savings, currency)} per unit
                                                </div>
                                            )}
                                        </div>
                                    ) : (
                                        <div className="mt-4 flex flex-col">
                                            <div className="flex items-end gap-3">
                                                <span className="text-3xl font-bold text-slate-900">{formatMoney(unitPrice, currency)}</span>
                                                <span className="text-sm font-medium text-slate-500 mb-1">/ unit</span>
                                            </div>
                                            <div className="mt-3 bg-amber-50 border border-amber-200 rounded-lg p-3 text-center">
                                                <p className="text-xs text-amber-800 font-medium">
                                                    Want lower prices? <br/>
                                                    <a href="/b2b/register" className="text-amber-900 font-bold underline hover:text-amber-700 transition">Click here to open a Wholesaler account</a>
                                                </p>
                                            </div>
                                        </div>
                                    )}

                                    <div className="mt-6 flex flex-col gap-2">
                                        <label htmlFor="quantity" className="text-sm font-medium text-slate-700 flex justify-between">
                                            <span>Quantity</span>
                                            <span className="text-xs text-slate-500">Min: {minimumOrder}</span>
                                        </label>
                                        <div className="flex items-center rounded-lg border border-slate-200 bg-slate-50 p-1">
                                            <button
                                                type="button"
                                                disabled={!canPurchase || safeQuantity <= minimumOrder}
                                                onClick={() => setQuantity((c) => Math.max(minimumOrder, c - 1))}
                                                className="flex h-8 w-8 items-center justify-center rounded-md bg-white text-slate-600 shadow-sm border border-slate-200 hover:bg-slate-100 disabled:opacity-50 transition active:scale-95"
                                            >
                                                &minus;
                                            </button>
                                            <input
                                                id="quantity"
                                                type="number"
                                                min={minimumOrder}
                                                max={Math.max(minimumOrder, availableStock)}
                                                value={safeQuantity}
                                                disabled={!canPurchase}
                                                onChange={(e) => {
                                                    const val = Number(e.target.value || minimumOrder);
                                                    setQuantity(Math.min(Math.max(minimumOrder, val), Math.max(minimumOrder, availableStock)));
                                                }}
                                                className="w-full border-none bg-transparent text-center text-sm font-bold text-slate-900 focus:ring-0"
                                            />
                                            <button
                                                type="button"
                                                disabled={!canPurchase || safeQuantity >= Math.max(minimumOrder, availableStock)}
                                                onClick={() => setQuantity((c) => Math.min(Math.max(minimumOrder, availableStock), c + 1))}
                                                className="flex h-8 w-8 items-center justify-center rounded-md bg-white text-slate-600 shadow-sm border border-slate-200 hover:bg-slate-100 disabled:opacity-50 transition active:scale-95"
                                            >
                                                +
                                            </button>
                                        </div>

                                        {/* Dynamic Quantity Slider */}
                                        {canPurchase && sliderMax > minimumOrder && (
                                            <div className="px-1 mt-3 mb-1">
                                                <input 
                                                    type="range"
                                                    min={minimumOrder}
                                                    max={sliderMax}
                                                    value={safeQuantity}
                                                    onChange={(e) => {
                                                        const val = Number(e.target.value || minimumOrder);
                                                        setQuantity(Math.min(Math.max(minimumOrder, val), Math.max(minimumOrder, availableStock)));
                                                    }}
                                                    className="w-full h-2 bg-slate-200 rounded-lg appearance-none cursor-pointer accent-blue-600 outline-none focus:ring-2 focus:ring-blue-500/30 transition-all hover:bg-slate-300"
                                                />
                                                <div className="flex justify-between text-[10px] font-medium text-slate-400 mt-1.5 px-0.5">
                                                    <span>Min: {minimumOrder}</span>
                                                    <span>{sliderMax >= availableStock ? 'Max Stock' : `${sliderMax}+`}</span>
                                                </div>
                                            </div>
                                        )}
                                    </div>

                                    <div className="flex justify-between items-center mt-4 py-4 border-t border-slate-100">
                                        <span className="text-sm font-medium text-slate-600">Total Price</span>
                                        <span className="text-lg font-bold text-slate-900">{formatMoney(lineTotal, currency)}</span>
                                    </div>

                                    <div className="flex flex-col gap-3 pb-6">
                                        <button
                                            onClick={addToCart}
                                            disabled={!canPurchase}
                                            className="w-full rounded-lg bg-blue-50 py-3 text-sm font-bold text-blue-700 transition hover:bg-blue-100 disabled:cursor-not-allowed disabled:opacity-50"
                                        >
                                            Add to Cart
                                        </button>
                                        <div className="flex gap-3">
                                            <button
                                                onClick={buyNow}
                                                disabled={!canPurchase}
                                                className="flex-1 rounded-lg bg-blue-600 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-50"
                                            >
                                                Buy Now
                                            </button>
                                            {isB2B && safeQuantity >= minimumOrder && (
                                                <button
                                                    onClick={() => {
                                                        rfqForm.setData('quantity', safeQuantity);
                                                        setIsRfqModalOpen(true);
                                                    }}
                                                    className="flex-1 rounded-lg border border-slate-200 bg-white py-3 text-sm font-bold text-slate-700 shadow-sm transition hover:bg-slate-50"
                                                >
                                                    Request a Quote
                                                </button>
                                            )}
                                        </div>
                                    </div>
                                </div>
                                
                                {/* Assist Actions (Save, Compare) */}
                                <div className="grid grid-cols-2 divide-x divide-slate-100 border-t border-slate-100 bg-slate-50 rounded-b-2xl">
                                    <button className="flex items-center justify-center gap-2 py-3 text-sm font-medium text-slate-600 hover:text-blue-600 hover:bg-blue-50/50 transition">
                                        <svg className="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                                        Save
                                    </button>
                                    <button className="flex items-center justify-center gap-2 py-3 text-sm font-medium text-slate-600 hover:text-blue-600 hover:bg-blue-50/50 transition">
                                        <svg className="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                                        Compare
                                    </button>
                                </div>
                            </div>
                            
                            {/* Policies List */}
                            <div className="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm space-y-4">
                                <div className="flex items-start gap-3">
                                    <div className="flex shrink-0 h-8 w-8 items-center justify-center rounded-full bg-blue-50 text-blue-600">
                                        <svg className="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
                                    </div>
                                    <div>
                                        <h4 className="text-sm font-bold text-slate-900">Free shipping</h4>
                                        <p className="text-xs text-slate-500 mt-0.5">Delivery: 5-7 business days</p>
                                    </div>
                                </div>
                                <div className="flex items-start gap-3">
                                    <div className="flex shrink-0 h-8 w-8 items-center justify-center rounded-full bg-blue-50 text-blue-600">
                                        <svg className="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                    </div>
                                    <div>
                                        <h4 className="text-sm font-bold text-slate-900">Return & refund policy</h4>
                                        <p className="text-xs text-slate-500 mt-0.5">7-day easy return on eligible products.</p>
                                    </div>
                                </div>
                                <div className="flex items-start gap-3">
                                    <div className="flex shrink-0 h-8 w-8 items-center justify-center rounded-full bg-blue-50 text-blue-600">
                                        <svg className="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                                    </div>
                                    <div>
                                        <h4 className="text-sm font-bold text-slate-900">Security & Privacy</h4>
                                        <p className="text-xs text-slate-500 mt-0.5">Protected checkout and encrypted payment.</p>
                                    </div>
                                </div>
                            </div>

                            {/* Share Row */}
                            <div className="flex items-center justify-center gap-4 py-2">
                                <span className="text-sm font-semibold text-slate-500">Share:</span>
                                <button className="text-slate-400 hover:text-blue-600 transition"><svg className="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/></svg></button>
                                <button className="text-slate-400 hover:text-blue-800 transition"><svg className="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M22.675 0h-21.35c-.732 0-1.325.593-1.325 1.325v21.351c0 .731.593 1.324 1.325 1.324h11.495v-9.294h-3.128v-3.622h3.128v-2.671c0-3.1 1.893-4.788 4.659-4.788 1.325 0 2.463.099 2.795.143v3.24l-1.918.001c-1.504 0-1.795.715-1.795 1.763v2.313h3.587l-.467 3.622h-3.12v9.293h6.116c.73 0 1.323-.593 1.323-1.325v-21.35c0-.732-.593-1.325-1.325-1.325z"/></svg></button>
                                <button className="text-slate-400 hover:text-red-600 transition"><svg className="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0C5.373 0 0 5.372 0 12c0 5.084 3.163 9.426 7.627 11.174-.105-.949-.2-2.405.042-3.441.218-.937 1.407-5.965 1.407-5.965s-.359-.719-.359-1.782c0-1.668.967-2.914 2.171-2.914 1.023 0 1.518.769 1.518 1.69 0 1.029-.655 2.568-.994 3.995-.283 1.194.599 2.169 1.777 2.169 2.133 0 3.772-2.249 3.772-5.495 0-2.873-2.064-4.882-5.012-4.882-3.414 0-5.418 2.561-5.418 5.207 0 1.031.397 2.138.893 2.738.098.119.112.224.083.345l-.333 1.36c-.053.22-.174.267-.401.161-1.499-.698-2.436-2.889-2.436-4.649 0-3.785 2.75-7.262 7.929-7.262 4.163 0 7.398 2.967 7.398 6.923 0 4.136-2.607 7.464-6.227 7.464-1.216 0-2.359-.631-2.75-1.378l-.748 2.853c-.271 1.043-1.002 2.35-1.492 3.146 1.124.347 2.317.535 3.55.535 6.627 0 12-5.373 12-12 0-6.628-5.373-12-12-12z"/></svg></button>
                            </div>
                            
                            {isB2B && Array.isArray(product.pricing_tiers) && product.pricing_tiers.length > 0 && (
                                <TierTable
                                    tiers={product.pricing_tiers || []}
                                    quantity={safeQuantity}
                                    currency={currency}
                                    basePrice={product.base_price}
                                    onSelectTier={(qty) => setQuantity(Math.min(Math.max(minimumOrder, qty), Math.max(minimumOrder, availableStock)))}
                                />
                            )}
                        </div>
                    </div>

                    {/* Full Page Details Tabs (Overview, Specifications, Reviews, FAQ) */}
                    <div className="mt-12 rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                        <div className="flex overflow-x-auto border-b border-slate-200 hide-scrollbar bg-slate-50">
                            {['overview', 'specifications', 'reviews', 'faq'].map(tab => (
                                <button 
                                    key={tab}
                                    onClick={() => setActiveTab(tab)}
                                    className={`px-8 py-4 text-sm font-bold uppercase tracking-wider whitespace-nowrap transition-colors border-b-2 ${activeTab === tab ? 'border-blue-600 text-blue-700 bg-white' : 'border-transparent text-slate-500 hover:text-slate-800'}`}
                                >
                                    {tab}
                                </button>
                            ))}
                        </div>
                        <div className="p-8">
                            {activeTab === 'overview' && (
                                <div className="prose prose-slate max-w-none text-slate-600 space-y-6">
                                    <h3 className="text-2xl font-bold text-slate-900">Product Overview</h3>
                                    <p>Experience the ultimate combination of performance, design, and innovation with the {product.name}. Built for professionals and enthusiasts alike, it brings cutting-edge technology right to your fingertips.</p>
                                    <p><strong>Key Benefits:</strong></p>
                                    <ul className="list-disc pl-5 space-y-2">
                                        <li>High-end durability with premium aerospace-grade materials.</li>
                                        <li>Advanced thermal management keeping it cool under heavy loads.</li>
                                        <li>Seamless connectivity supporting all modern wireless standards.</li>
                                        <li>Optimized for low power consumption, saving you energy costs over time.</li>
                                        <li>Backed by a comprehensive warranty and 24/7 customer support.</li>
                                    </ul>
                                    <div className="mt-8 grid grid-cols-1 sm:grid-cols-2 gap-6">
                                        <div className="bg-slate-50 p-6 rounded-xl border border-slate-100">
                                            <h4 className="font-bold text-slate-900 mb-2">Unmatched Performance</h4>
                                            <p className="text-sm">Powered by the latest generation architecture, delivering up to 40% more speed than previous iterations.</p>
                                        </div>
                                        <div className="bg-slate-50 p-6 rounded-xl border border-slate-100">
                                            <h4 className="font-bold text-slate-900 mb-2">Eco-friendly Design</h4>
                                            <p className="text-sm">Manufactured using 100% recycled materials in its core casing, reducing environmental impact.</p>
                                        </div>
                                    </div>
                                    <p className="mt-6">Whether you are upgrading your current setup or investing in a reliable new solution, the {product.name} stands out as the premier choice in its category.</p>
                                </div>
                            )}
                            {activeTab === 'specifications' && (
                                <div className="space-y-8">
                                    <h3 className="text-2xl font-bold text-slate-900">Technical Specifications</h3>
                                    <div className="overflow-x-auto">
                                        <table className="w-full text-left text-sm border-collapse">
                                            <tbody className="divide-y divide-slate-200">
                                                <tr className="bg-slate-50"><th colSpan="2" className="px-4 py-2 font-bold text-slate-800 uppercase tracking-wider text-xs">General Information</th></tr>
                                                <tr><td className="px-4 py-3 font-medium text-slate-500 w-1/3">Brand</td><td className="px-4 py-3 text-slate-900">{product.brand?.name || 'Plexus Generic'}</td></tr>
                                                <tr><td className="px-4 py-3 font-medium text-slate-500 w-1/3">Model Year</td><td className="px-4 py-3 text-slate-900">2026</td></tr>
                                                <tr><td className="px-4 py-3 font-medium text-slate-500 w-1/3">Color Options</td><td className="px-4 py-3 text-slate-900">Midnight Black, Silver, Titanium</td></tr>
                                                
                                                <tr className="bg-slate-50"><th colSpan="2" className="px-4 py-2 font-bold text-slate-800 uppercase tracking-wider text-xs">Physical Dimensions</th></tr>
                                                <tr><td className="px-4 py-3 font-medium text-slate-500 w-1/3">Weight</td><td className="px-4 py-3 text-slate-900">1.2 kg</td></tr>
                                                <tr><td className="px-4 py-3 font-medium text-slate-500 w-1/3">Dimensions (L x W x H)</td><td className="px-4 py-3 text-slate-900">20.5 x 15.2 x 10.0 cm</td></tr>
                                                <tr><td className="px-4 py-3 font-medium text-slate-500 w-1/3">Material</td><td className="px-4 py-3 text-slate-900">Aerospace-grade Aluminum alloy</td></tr>
                                                
                                                <tr className="bg-slate-50"><th colSpan="2" className="px-4 py-2 font-bold text-slate-800 uppercase tracking-wider text-xs">Performance & Hardware</th></tr>
                                                <tr><td className="px-4 py-3 font-medium text-slate-500 w-1/3">Processor / Core</td><td className="px-4 py-3 text-slate-900">Octa-core Ultra Processor</td></tr>
                                                <tr><td className="px-4 py-3 font-medium text-slate-500 w-1/3">Memory (RAM)</td><td className="px-4 py-3 text-slate-900">16 GB Unified Memory</td></tr>
                                                <tr><td className="px-4 py-3 font-medium text-slate-500 w-1/3">Storage Capacity</td><td className="px-4 py-3 text-slate-900">512 GB NVMe SSD</td></tr>
                                                
                                                <tr className="bg-slate-50"><th colSpan="2" className="px-4 py-2 font-bold text-slate-800 uppercase tracking-wider text-xs">Connectivity</th></tr>
                                                <tr><td className="px-4 py-3 font-medium text-slate-500 w-1/3">Wireless</td><td className="px-4 py-3 text-slate-900">Wi-Fi 7 (802.11be), Bluetooth 5.4</td></tr>
                                                <tr><td className="px-4 py-3 font-medium text-slate-500 w-1/3">Ports</td><td className="px-4 py-3 text-slate-900">2x USB-C (Thunderbolt 4), 1x HDMI 2.1, 1x Audio Jack</td></tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            )}
                            {activeTab === 'reviews' && (
                                <div className="space-y-8">
                                    <div className="flex flex-col md:flex-row items-center gap-8 border-b border-slate-100 pb-8">
                                        <div className="text-center md:text-left">
                                            <h3 className="text-5xl font-bold text-slate-900">4.2</h3>
                                            <div className="flex text-amber-400 text-lg my-2 justify-center md:justify-start">
                                                <span>★</span><span>★</span><span>★</span><span>★</span><span className="text-slate-300">★</span>
                                            </div>
                                            <p className="text-sm text-slate-500">Based on 128 Reviews</p>
                                        </div>
                                        <div className="flex-1 w-full space-y-2">
                                            {[
                                                { stars: 5, percent: 65 },
                                                { stars: 4, percent: 20 },
                                                { stars: 3, percent: 10 },
                                                { stars: 2, percent: 3 },
                                                { stars: 1, percent: 2 },
                                            ].map(rating => (
                                                <div key={rating.stars} className="flex items-center gap-3 text-sm">
                                                    <span className="w-12 font-medium text-slate-600">{rating.stars} Stars</span>
                                                    <div className="flex-1 h-2 bg-slate-100 rounded-full overflow-hidden">
                                                        <div className="h-full bg-amber-400 rounded-full" style={{ width: `${rating.percent}%` }}></div>
                                                    </div>
                                                    <span className="w-10 text-right text-slate-500">{rating.percent}%</span>
                                                </div>
                                            ))}
                                        </div>
                                        <div>
                                            <button className="px-6 py-3 bg-blue-600 text-white font-bold rounded-lg hover:bg-blue-700 transition">Write a Review</button>
                                        </div>
                                    </div>
                                    <div className="space-y-6">
                                        <div className="pb-6 border-b border-slate-100">
                                            <div className="flex justify-between items-start mb-2">
                                                <div className="flex items-center gap-3">
                                                    <div className="h-10 w-10 rounded-full bg-slate-200 flex items-center justify-center font-bold text-slate-500">JD</div>
                                                    <div>
                                                        <h4 className="font-bold text-slate-900">John Doe</h4>
                                                        <div className="flex text-amber-400 text-xs">
                                                            <span>★</span><span>★</span><span>★</span><span>★</span><span>★</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <span className="text-xs text-slate-400">2 days ago</span>
                                            </div>
                                            <p className="text-sm text-slate-600 leading-relaxed mt-3">Absolutely fantastic product! Exceeded all my expectations in terms of build quality and performance. The delivery was super fast too. Highly recommended for anyone looking for this kind of product.</p>
                                        </div>
                                        <div className="pb-6 border-b border-slate-100">
                                            <div className="flex justify-between items-start mb-2">
                                                <div className="flex items-center gap-3">
                                                    <div className="h-10 w-10 rounded-full bg-slate-200 flex items-center justify-center font-bold text-slate-500">AS</div>
                                                    <div>
                                                        <h4 className="font-bold text-slate-900">Alice Smith</h4>
                                                        <div className="flex text-amber-400 text-xs">
                                                            <span>★</span><span>★</span><span>★</span><span>★</span><span className="text-slate-300">★</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <span className="text-xs text-slate-400">1 week ago</span>
                                            </div>
                                            <p className="text-sm text-slate-600 leading-relaxed mt-3">Good value for the money. It does exactly what it says on the box. Only giving 4 stars because the packaging was a bit damaged when it arrived, but the product inside was perfectly fine.</p>
                                        </div>
                                    </div>
                                </div>
                            )}
                            {activeTab === 'faq' && (
                                <div className="space-y-6">
                                    <h3 className="text-2xl font-bold text-slate-900 mb-6">Frequently Asked Questions</h3>
                                    <div className="space-y-4">
                                        <div className="border border-slate-200 rounded-xl p-5 bg-slate-50">
                                            <h4 className="font-bold text-slate-900 mb-2">Q: Does this come with a warranty?</h4>
                                            <p className="text-sm text-slate-600">A: Yes, all products come with a standard 1-year manufacturer warranty covering any defects in materials and workmanship.</p>
                                        </div>
                                        <div className="border border-slate-200 rounded-xl p-5 bg-slate-50">
                                            <h4 className="font-bold text-slate-900 mb-2">Q: How long does shipping usually take?</h4>
                                            <p className="text-sm text-slate-600">A: For standard delivery, it usually takes 5-7 business days. Express shipping is available at checkout for 2-day delivery.</p>
                                        </div>
                                        <div className="border border-slate-200 rounded-xl p-5 bg-slate-50">
                                            <h4 className="font-bold text-slate-900 mb-2">Q: What is the return policy?</h4>
                                            <p className="text-sm text-slate-600">A: We offer a 7-day easy return policy for all eligible items provided they are unused and in their original packaging.</p>
                                        </div>
                                    </div>
                                </div>
                            )}
                        </div>
                    </div>

                    {/* Related Products */}
                    <div className="mt-16 space-y-12">
                        {relatedProducts && relatedProducts.length > 0 && (
                            <section>
                                <div className="mb-6 flex items-center justify-between">
                                    <h2 className="text-xl font-bold text-slate-900">Similar Products</h2>
                                    <Link href={route('products.index')} className="text-sm font-medium text-blue-600 hover:underline">View all</Link>
                                </div>
                                <div className="grid grid-cols-1 gap-6 sm:grid-cols-2 md:grid-cols-4 lg:grid-cols-5">
                                    {relatedProducts.slice(0, 5).map((item) => (
                                        <SuggestionCard key={item.id} product={item} currency={currency} />
                                    ))}
                                </div>
                            </section>
                        )}
                        
                        {supplierProducts && supplierProducts.length > 0 && (
                            <section>
                                <div className="mb-6 flex items-center justify-between">
                                    <h2 className="text-xl font-bold text-slate-900">More from {product.supplier?.company_name || 'this supplier'}</h2>
                                </div>
                                <div className="grid grid-cols-1 gap-6 sm:grid-cols-2 md:grid-cols-4 lg:grid-cols-5">
                                    {supplierProducts.slice(0, 5).map((item) => (
                                        <SuggestionCard key={item.id} product={item} currency={currency} />
                                    ))}
                                </div>
                            </section>
                        )}
                    </div>
                </main>
            </div>
            
            <Transition appear show={isRfqModalOpen} as={Fragment}>
                <Dialog as="div" className="relative z-50" onClose={() => setIsRfqModalOpen(false)}>
                    <Transition.Child
                        as={Fragment}
                        enter="ease-out duration-300"
                        enterFrom="opacity-0"
                        enterTo="opacity-100"
                        leave="ease-in duration-200"
                        leaveFrom="opacity-100"
                        leaveTo="opacity-0"
                    >
                        <div className="fixed inset-0 bg-slate-900/40 backdrop-blur-sm" />
                    </Transition.Child>

                    <div className="fixed inset-0 overflow-y-auto">
                        <div className="flex min-h-full items-center justify-center p-4 text-center">
                            <Transition.Child
                                as={Fragment}
                                enter="ease-out duration-300"
                                enterFrom="opacity-0 scale-95"
                                enterTo="opacity-100 scale-100"
                                leave="ease-in duration-200"
                                leaveFrom="opacity-100 scale-100"
                                leaveTo="opacity-0 scale-95"
                            >
                                <Dialog.Panel className="w-full max-w-md transform overflow-hidden rounded-2xl bg-white p-6 text-left align-middle shadow-xl transition-all">
                                    <Dialog.Title as="h3" className="text-lg font-medium leading-6 text-slate-900">
                                        Request a Custom Quote
                                    </Dialog.Title>
                                    <form onSubmit={submitRfq} className="mt-4 space-y-4">
                                        <div>
                                            <label className="block text-sm font-medium text-slate-700">Target Price / Unit</label>
                                            <input
                                                type="number"
                                                min="0.01"
                                                step="0.01"
                                                className="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                                value={rfqForm.data.target_price}
                                                onChange={e => rfqForm.setData('target_price', e.target.value)}
                                            />
                                        </div>
                                        <div>
                                            <label className="block text-sm font-medium text-slate-700">Quantity</label>
                                            <input
                                                type="number"
                                                min={minimumOrder}
                                                className="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                                value={rfqForm.data.quantity}
                                                onChange={e => rfqForm.setData('quantity', e.target.value)}
                                            />
                                        </div>
                                        <div>
                                            <label className="block text-sm font-medium text-slate-700">Message</label>
                                            <textarea
                                                rows={4}
                                                className="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                                value={rfqForm.data.message}
                                                onChange={e => rfqForm.setData('message', e.target.value)}
                                            />
                                        </div>
                                        <div className="mt-6 flex justify-end gap-3">
                                            <button
                                                type="button"
                                                onClick={() => setIsRfqModalOpen(false)}
                                                className="inline-flex justify-center rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                                            >
                                                Cancel
                                            </button>
                                            <button
                                                type="submit"
                                                disabled={rfqForm.processing}
                                                className="inline-flex justify-center rounded-md border border-transparent bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50"
                                            >
                                                {rfqForm.processing ? 'Submitting...' : 'Submit Request'}
                                            </button>
                                        </div>
                                    </form>
                                </Dialog.Panel>
                            </Transition.Child>
                        </div>
                    </div>
                </Dialog>
            </Transition>
        </FrontendLayout>
    );
}
