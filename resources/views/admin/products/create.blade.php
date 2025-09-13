@extends('layouts.app')

@section('content')
<div style="min-height:100vh;background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);padding:20px 0">
    <div style="max-width:1000px;margin:0 auto;padding:0 20px">
        
        <!-- Header Section -->
        <div style="background:white;border-radius:12px;box-shadow:0 8px 32px rgba(0,0,0,0.1);margin-bottom:24px;padding:24px">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px">
                <div style="display:flex;align-items:center;gap:12px">
                    <div style="background:linear-gradient(135deg,#667eea,#764ba2);color:white;padding:12px;border-radius:8px;display:flex;align-items:center;justify-content:center">
                        <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                    </div>
                    <div>
                        <h1 style="font-family:'Courier New',monospace;font-size:28px;font-weight:700;color:#1f2937;margin:0">Create New Product</h1>
                        <p style="color:#6b7280;margin:0;font-size:16px">Add a new product to your inventory system</p>
                    </div>
                </div>
                <a href="{{ route('staffmod.admin.products') }}" 
                   style="background:#f3f4f6;color:#374151;text-decoration:none;padding:10px 16px;border-radius:8px;font-family:'Courier New',monospace;font-weight:500;display:flex;align-items:center;gap:8px;transition:all 0.2s"
                   onmouseover="this.style.background='#e5e7eb'" 
                   onmouseout="this.style.background='#f3f4f6'">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Products
                </a>
            </div>
        </div>

        <!-- Main Form Card -->
        <div style="background:white;border-radius:12px;box-shadow:0 8px 32px rgba(0,0,0,0.1);overflow:hidden">
            <form method="POST" action="{{ route('staffmod.admin.products.store') }}" enctype="multipart/form-data">
                @csrf
                
                <!-- Form Header -->
                <div style="background:linear-gradient(135deg,#f8fafc,#e2e8f0);padding:24px;border-bottom:1px solid #e5e7eb">
                    <h2 style="font-family:'Courier New',monospace;font-size:20px;font-weight:600;color:#1f2937;margin:0;display:flex;align-items:center;gap:8px">
                        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Product Information
                    </h2>
                    <p style="color:#6b7280;margin:8px 0 0 0;font-size:14px">Fill in the details below to create a new product</p>
                </div>

                <!-- Form Content -->
                <div style="padding:32px">
                    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(300px,1fr));gap:24px">
                        
                        <!-- Basic Information Section -->
                        <div style="background:#f8fafc;padding:24px;border-radius:8px;border:1px solid #e5e7eb">
                            <h3 style="font-family:'Courier New',monospace;font-size:16px;font-weight:600;color:#374151;margin:0 0 20px 0;display:flex;align-items:center;gap:8px">
                                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Basic Information
                            </h3>
                            
                            <div style="display:grid;gap:16px">
                                <div style="display:grid;grid-template-columns:1fr 2fr;gap:12px">
                                    <div>
                                        <label style="display:block;font-family:'Courier New',monospace;font-weight:600;color:#374151;margin-bottom:6px;font-size:14px">SKU *</label>
                                        <input name="sku" value="{{ old('sku') }}" 
                                               style="width:100%;padding:12px;border:2px solid #e5e7eb;border-radius:6px;font-family:'Courier New',monospace;transition:all 0.2s;font-size:14px"
                                               onfocus="this.style.borderColor='#667eea';this.style.boxShadow='0 0 0 3px rgba(102,126,234,0.1)'"
                                               onblur="this.style.borderColor='#e5e7eb';this.style.boxShadow='none'"
                                               placeholder="Enter SKU" required>
                                        @error('sku')<div style="color:#ef4444;font-size:12px;margin-top:4px;font-family:'Courier New',monospace">{{ $message }}</div>@enderror
                                    </div>
                                    <div>
                                        <label style="display:block;font-family:'Courier New',monospace;font-weight:600;color:#374151;margin-bottom:6px;font-size:14px">Product Name *</label>
                                        <input name="name" value="{{ old('name') }}" 
                                               style="width:100%;padding:12px;border:2px solid #e5e7eb;border-radius:6px;font-family:'Courier New',monospace;transition:all 0.2s;font-size:14px"
                                               onfocus="this.style.borderColor='#667eea';this.style.boxShadow='0 0 0 3px rgba(102,126,234,0.1)'"
                                               onblur="this.style.borderColor='#e5e7eb';this.style.boxShadow='none'"
                                               placeholder="Enter product name" required>
                                        @error('name')<div style="color:#ef4444;font-size:12px;margin-top:4px;font-family:'Courier New',monospace">{{ $message }}</div>@enderror
                                    </div>
                                </div>

                                <div>
                                    <label style="display:block;font-family:'Courier New',monospace;font-weight:600;color:#374151;margin-bottom:6px;font-size:14px">Category *</label>
                                    <select name="category" 
                                            style="width:100%;padding:12px;border:2px solid #e5e7eb;border-radius:6px;font-family:'Courier New',monospace;transition:all 0.2s;font-size:14px;background:white"
                                            onfocus="this.style.borderColor='#667eea';this.style.boxShadow='0 0 0 3px rgba(102,126,234,0.1)'"
                                            onblur="this.style.borderColor='#e5e7eb';this.style.boxShadow='none'"
                                            required>
                                        <option value="">-- Select Category --</option>
                                        <option value="Medication" {{ old('category')=='Medication'?'selected':'' }}>💊 Medication</option>
                                        <option value="Supplement" {{ old('category')=='Supplement'?'selected':'' }}>🧪 Supplement</option>
                                        <option value="Equipment" {{ old('category')=='Equipment'?'selected':'' }}>🏥 Equipment</option>
                                    </select>
                                    @error('category')<div style="color:#ef4444;font-size:12px;margin-top:4px;font-family:'Courier New',monospace">{{ $message }}</div>@enderror
                                </div>

                                <div>
                                    <label style="display:block;font-family:'Courier New',monospace;font-weight:600;color:#374151;margin-bottom:6px;font-size:14px">Description</label>
                                    <textarea name="description" rows="3"
                                              style="width:100%;padding:12px;border:2px solid #e5e7eb;border-radius:6px;font-family:'Courier New',monospace;transition:all 0.2s;font-size:14px;resize:vertical"
                                              onfocus="this.style.borderColor='#667eea';this.style.boxShadow='0 0 0 3px rgba(102,126,234,0.1)'"
                                              onblur="this.style.borderColor='#e5e7eb';this.style.boxShadow='none'"
                                              placeholder="Enter product description">{{ old('description') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Pricing & Inventory Section -->
                        <div style="background:#f8fafc;padding:24px;border-radius:8px;border:1px solid #e5e7eb">
                            <h3 style="font-family:'Courier New',monospace;font-size:16px;font-weight:600;color:#374151;margin:0 0 20px 0;display:flex;align-items:center;gap:8px">
                                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                                </svg>
                                Pricing & Inventory
                            </h3>
                            
                            <div style="display:grid;gap:16px">
                                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                                    <div>
                                        <label style="display:block;font-family:'Courier New',monospace;font-weight:600;color:#374151;margin-bottom:6px;font-size:14px">Price ($) *</label>
                                        <input type="number" step="0.01" min="0" name="price" value="{{ old('price') }}" 
                                               style="width:100%;padding:12px;border:2px solid #e5e7eb;border-radius:6px;font-family:'Courier New',monospace;transition:all 0.2s;font-size:14px"
                                               onfocus="this.style.borderColor='#667eea';this.style.boxShadow='0 0 0 3px rgba(102,126,234,0.1)'"
                                               onblur="this.style.borderColor='#e5e7eb';this.style.boxShadow='none'"
                                               placeholder="0.00" required>
                                        @error('price')<div style="color:#ef4444;font-size:12px;margin-top:4px;font-family:'Courier New',monospace">{{ $message }}</div>@enderror
                                    </div>
                                    <div>
                                        <label style="display:block;font-family:'Courier New',monospace;font-weight:600;color:#374151;margin-bottom:6px;font-size:14px">Cost ($) *</label>
                                        <input type="number" step="0.01" min="0" name="cost" value="{{ old('cost') }}" 
                                               style="width:100%;padding:12px;border:2px solid #e5e7eb;border-radius:6px;font-family:'Courier New',monospace;transition:all 0.2s;font-size:14px"
                                               onfocus="this.style.borderColor='#667eea';this.style.boxShadow='0 0 0 3px rgba(102,126,234,0.1)'"
                                               onblur="this.style.borderColor='#e5e7eb';this.style.boxShadow='none'"
                                               placeholder="0.00" required>
                                        @error('cost')<div style="color:#ef4444;font-size:12px;margin-top:4px;font-family:'Courier New',monospace">{{ $message }}</div>@enderror
                                    </div>
                                </div>

                                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                                    <div>
                                        <label style="display:block;font-family:'Courier New',monospace;font-weight:600;color:#374151;margin-bottom:6px;font-size:14px">Quantity *</label>
                                        <input type="number" min="0" name="quantity" value="{{ old('quantity') }}" 
                                               style="width:100%;padding:12px;border:2px solid #e5e7eb;border-radius:6px;font-family:'Courier New',monospace;transition:all 0.2s;font-size:14px"
                                               onfocus="this.style.borderColor='#667eea';this.style.boxShadow='0 0 0 3px rgba(102,126,234,0.1)'"
                                               onblur="this.style.borderColor='#e5e7eb';this.style.boxShadow='none'"
                                               placeholder="0" required>
                                        @error('quantity')<div style="color:#ef4444;font-size:12px;margin-top:4px;font-family:'Courier New',monospace">{{ $message }}</div>@enderror
                                    </div>
                                    <div>
                                        <label style="display:block;font-family:'Courier New',monospace;font-weight:600;color:#374151;margin-bottom:6px;font-size:14px">Unit</label>
                                        <input name="unit" value="{{ old('unit', 'pcs') }}" 
                                               style="width:100%;padding:12px;border:2px solid #e5e7eb;border-radius:6px;font-family:'Courier New',monospace;transition:all 0.2s;font-size:14px"
                                               onfocus="this.style.borderColor='#667eea';this.style.boxShadow='0 0 0 3px rgba(102,126,234,0.1)'"
                                               onblur="this.style.borderColor='#e5e7eb';this.style.boxShadow='none'"
                                               placeholder="pcs, kg, ml, etc.">
                                    </div>
                                </div>

                                <div>
                                    <label style="display:block;font-family:'Courier New',monospace;font-weight:600;color:#374151;margin-bottom:6px;font-size:14px">Reorder Level</label>
                                    <input type="number" min="0" name="reorder_level" value="{{ old('reorder_level', 5) }}" 
                                           style="width:100%;padding:12px;border:2px solid #e5e7eb;border-radius:6px;font-family:'Courier New',monospace;transition:all 0.2s;font-size:14px"
                                           onfocus="this.style.borderColor='#667eea';this.style.boxShadow='0 0 0 3px rgba(102,126,234,0.1)'"
                                           onblur="this.style.borderColor='#e5e7eb';this.style.boxShadow='none'"
                                           placeholder="5">
                                    <div style="color:#6b7280;font-size:12px;margin-top:4px;font-family:'Courier New',monospace">Stock alert will trigger when quantity reaches this level</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Details Section -->
                    <div style="background:#f8fafc;padding:24px;border-radius:8px;border:1px solid #e5e7eb;margin-top:24px">
                        <h3 style="font-family:'Courier New',monospace;font-size:16px;font-weight:600;color:#374151;margin:0 0 20px 0;display:flex;align-items:center;gap:8px">
                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                            Additional Details
                        </h3>
                        
                        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:16px">
                            <div>
                                <label style="display:block;font-family:'Courier New',monospace;font-weight:600;color:#374151;margin-bottom:6px;font-size:14px">Manufacturer</label>
                                <input name="manufacturer" value="{{ old('manufacturer') }}" 
                                       style="width:100%;padding:12px;border:2px solid #e5e7eb;border-radius:6px;font-family:'Courier New',monospace;transition:all 0.2s;font-size:14px"
                                       onfocus="this.style.borderColor='#667eea';this.style.boxShadow='0 0 0 3px rgba(102,126,234,0.1)'"
                                       onblur="this.style.borderColor='#e5e7eb';this.style.boxShadow='none'"
                                       placeholder="Enter manufacturer name">
                            </div>
                            <div>
                                <label style="display:block;font-family:'Courier New',monospace;font-weight:600;color:#374151;margin-bottom:6px;font-size:14px">Expiration Date</label>
                                <input type="date" name="expiration_date" value="{{ old('expiration_date') }}" 
                                       style="width:100%;padding:12px;border:2px solid #e5e7eb;border-radius:6px;font-family:'Courier New',monospace;transition:all 0.2s;font-size:14px"
                                       onfocus="this.style.borderColor='#667eea';this.style.boxShadow='0 0 0 3px rgba(102,126,234,0.1)'"
                                       onblur="this.style.borderColor='#e5e7eb';this.style.boxShadow='none'">
                            </div>
                            <div>
                                <label style="display:block;font-family:'Courier New',monospace;font-weight:600;color:#374151;margin-bottom:6px;font-size:14px">Status</label>
                                <select name="is_active" 
                                        style="width:100%;padding:12px;border:2px solid #e5e7eb;border-radius:6px;font-family:'Courier New',monospace;transition:all 0.2s;font-size:14px;background:white"
                                        onfocus="this.style.borderColor='#667eea';this.style.boxShadow='0 0 0 3px rgba(102,126,234,0.1)'"
                                        onblur="this.style.borderColor='#e5e7eb';this.style.boxShadow='none'">
                                    <option value="1" {{ old('is_active', 1) == 1 ? 'selected' : '' }}>✅ Active</option>
                                    <option value="0" {{ old('is_active', 1) == 0 ? 'selected' : '' }}>❌ Inactive</option>
                                </select>
                            </div>
                        </div>

                        <div style="margin-top:16px">
                            <label style="display:block;font-family:'Courier New',monospace;font-weight:600;color:#374151;margin-bottom:6px;font-size:14px">Product Image</label>
                            <div style="border:2px dashed #d1d5db;border-radius:8px;padding:24px;text-align:center;background:#f9fafb;transition:all 0.2s"
                                 ondragover="event.preventDefault();this.style.borderColor='#667eea';this.style.background='#f0f4ff'"
                                 ondragleave="this.style.borderColor='#d1d5db';this.style.background='#f9fafb'"
                                 ondrop="event.preventDefault();this.style.borderColor='#d1d5db';this.style.background='#f9fafb'">
                                <svg width="48" height="48" fill="none" stroke="#9ca3af" viewBox="0 0 24 24" style="margin:0 auto 12px">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <input type="file" name="product_image" accept="image/*"
                                       style="width:100%;padding:8px;border:1px solid #d1d5db;border-radius:4px;font-family:'Courier New',monospace;font-size:14px;background:white">
                                <div style="color:#6b7280;font-size:12px;margin-top:8px;font-family:'Courier New',monospace">Upload JPG, PNG, or GIF (Max: 2MB)</div>
                                @error('product_image')<div style="color:#ef4444;font-size:12px;margin-top:4px;font-family:'Courier New',monospace">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div style="display:flex;gap:12px;justify-content:flex-end;margin-top:32px;padding-top:24px;border-top:1px solid #e5e7eb">
                        <a href="{{ route('staffmod.admin.products') }}" 
                           style="background:#f3f4f6;color:#374151;text-decoration:none;padding:12px 24px;border-radius:8px;font-family:'Courier New',monospace;font-weight:500;display:flex;align-items:center;gap:8px;transition:all 0.2s;border:2px solid #e5e7eb"
                           onmouseover="this.style.background='#e5e7eb'" 
                           onmouseout="this.style.background='#f3f4f6'">
                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            Cancel
                        </a>
                        <button type="submit" 
                                style="background:linear-gradient(135deg,#667eea,#764ba2);color:white;border:none;padding:12px 24px;border-radius:8px;font-family:'Courier New',monospace;font-weight:600;display:flex;align-items:center;gap:8px;cursor:pointer;transition:all 0.2s;box-shadow:0 4px 12px rgba(102,126,234,0.3)"
                                onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 8px 20px rgba(102,126,234,0.4)'" 
                                onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='0 4px 12px rgba(102,126,234,0.3)'">
                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Create Product
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Add some interactivity for better UX
document.addEventListener('DOMContentLoaded', function() {
    // Auto-calculate profit margin display
    const priceInput = document.querySelector('input[name="price"]');
    const costInput = document.querySelector('input[name="cost"]');
    
    function updateMargin() {
        const price = parseFloat(priceInput.value) || 0;
        const cost = parseFloat(costInput.value) || 0;
        if (price > 0 && cost > 0) {
            const margin = ((price - cost) / price * 100).toFixed(1);
            const marginColor = margin > 50 ? '#10b981' : margin > 25 ? '#f59e0b' : '#ef4444';
            
            // Remove existing margin display
            const existingMargin = document.querySelector('.margin-display');
            if (existingMargin) existingMargin.remove();
            
            // Add margin display
            if (margin >= 0) {
                const marginDiv = document.createElement('div');
                marginDiv.className = 'margin-display';
                marginDiv.style.cssText = `color:${marginColor};font-size:12px;margin-top:4px;font-family:'Courier New',monospace;font-weight:600`;
                marginDiv.textContent = `Profit Margin: ${margin}%`;
                costInput.parentNode.appendChild(marginDiv);
            }
        }
    }
    
    if (priceInput && costInput) {
        priceInput.addEventListener('input', updateMargin);
        costInput.addEventListener('input', updateMargin);
        updateMargin(); // Initial calculation
    }
});
</script>
@endsection


