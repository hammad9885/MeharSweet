@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">Product Registration - Edit</h4>
            </div>
        </div>
    </div>

    @if (session('success'))
    <div class="alert alert-success alert-dismissible text-bg-success border-0 fade show" role="alert">
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
        {{ session('success') }}
    </div>
    @endif

    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form id="voucherForm" action="{{ route('registration_form.update', $product->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT') <!-- Use PUT method for updating -->
                    
                    <div class="col-6">
                        <input type="hidden" id="invoice_type" name="v_type" value="LPN" readonly>
                        <input type="hidden" id="invoice" name="invoice_number" value="{{ $product->invoice_number }}">
                        <input type="hidden" id="totalAmount" name="total_amount" value="{{ $product->total_amount }}">

                        <div class="mb-3">
    <label for="product_type" class="form-label">Product Type</label>
    <select name="product_type" class="form-control select2" data-toggle="select2" id="product_type">
        <option value="">Select</option>
        <option value="Local" {{ (old('product_type', $product->product_type) == 'Local') ? 'selected' : '' }}>Local</option>
        <option value="Export" {{ (old('product_type', $product->product_type) == 'Export') ? 'selected' : '' }}>Export</option>
    </select>
</div>

                        
                        <!-- Supplier Selection -->
                        <div class="mb-3">
                            <label for="entryParty" class="form-label">Party</label>
                            <select name="account" class="form-control select2" id="entryParty" data-toggle="select2">
                                <option value="">Select</option>
                                @foreach ($accounts->where('level2_id', 7) as $account)
                               <option value="{{ $account->id }}" 
    {{ old('account', $product->account_id ?? $product->account->id ?? null) == $account->id ? 'selected' : '' }}>
    {{ $account->title }}
</option>

                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="productName" class="form-label">Product Name</label>
                            <input type="text" id="productName" class="form-control" name="productName" value="{{ $product->prod_name }}">
                        </div>

                        <!-- Country Dropdown -->
                        <div class="mb-3">
    <label for="country" class="form-label">Country</label>
    <select name="country" class="form-control select2" id="country" data-toggle="select2">
        <option value="">Select</option>
        @foreach ($countries as $country)
            <option value="{{ $country->id }}" 
                {{ old('country', $product->country_id) == $country->id ? 'selected' : '' }}>
                {{ $country->country_name }}
            </option>
        @endforeach
    </select>
</div>

<!-- Item Type Dropdown -->
<div class="mb-3">
    <label for="itemTitle" class="form-label">Item Type</label>
    <select name="item" class="form-control select2" data-toggle="select2" id="itemTitle">
        <option value="">Select</option>
        @foreach ($itemsAll as $item)
            <option value="{{ $item->id }}" 
                data-grammage="{{ $item->grammage }}"
                {{ old('item', $product->item_id) == $item->id ? 'selected' : '' }}>
                {{ $item->item_code }}
            </option>
        @endforeach
    </select>
</div>


                        <!-- Grammage Field -->
                        <div class="mb-3">
                            <label for="grammage" class="form-label">Grammage</label>
                            <input type="number" id="grammage" class="form-control" name="grammage" value="{{ $product->grammage }}" step="any">
                        </div>

                        <!-- Length Field -->
                        <div class="mb-3">
                            <label for="length" class="form-label">Length</label>
                            <input type="number" id="length" class="form-control" name="length" value="{{ $product->length }}" step="any">
                        </div>

                        <!-- Width Field -->
                        <div class="mb-3">
                            <label for="width" class="form-label">Width</label>
                            <input type="number" id="width" class="form-control" name="width" value="{{ $product->width }}" step="any">
                        </div>

                        <!-- Checkbox Section (Options) -->
                        <div class="card p-3 mt-3">
                            <h5 class="mb-3">Options</h5>

                            <!-- Lamination -->
                            <div class="form-check form-check-inline">
                                <input type="hidden" name="lamination" value="0">
                                <input class="form-check-input" type="checkbox" id="lamination" name="lamination" value="1" {{ $product->lamination ? 'checked' : '' }}>
                                <label class="form-check-label" for="lamination">Lamination</label>
                            </div>

                            <!-- Lamination Fields -->
                            <div id="laminationFields" style="{{ $product->lamination ? 'display:block;' : 'display:none;' }}">
                                <div class="mb-3"><br>
                                    <label for="lsize" class="form-label">Size</label>
                                    <input type="number" id="lsize" class="form-control" name="lsize" value="{{ $product->lam_size }}"  step="any">
                                </div>
                             <div class="mb-3">
    <label for="litem" class="form-label">Item Type</label>
    <select name="litem" class="form-control select2" data-toggle="select2" id="itemTitle">
        <option value="">Select</option>
        @foreach ($items as $item)
            <option value="{{ $item->id }}" 
                data-rate="{{ $item->purchase }}" 
                data-grammage="{{ $item->grammage }}"
                {{ old('litem', $product->lam_item) == $item->id ? 'selected' : '' }}>
                {{ $item->item_code }}
            </option>
        @endforeach
    </select>
</div>


<div class="mb-3"><br>
                                    <label for="limpression" class="form-label">No. of Impression</label>
                                    <input type="number" id="limpression" class="form-control" name="limpression"  value="{{ $product->limpression }}" step="any">
                                </div>


                            </div>

                            <!-- UV -->
                            <div class="form-check">
                                <input type="hidden" name="uv" value="0">
                                <input class="form-check-input" type="checkbox" id="uv" name="uv" value="1" {{ $product->uv ? 'checked' : '' }}>
                                <label class="form-check-label" for="uv">UV</label>
                            </div>
                            
                            <div id="uvOptions" style="display: none; margin-top: 10px; margin-bottom: 10px; margin-left: 20px;">
    <div class="form-check">
        <input class="form-check-input" type="checkbox" id="simple" name="simple" value="1" {{ $product->simple ? 'checked' : '' }}>
        <label class="form-check-label" for="simple">Simple</label>
    </div>
    <div id="simpleRateContainer" style="display: none;">
       <div class="form-check">
    <label for="simple_rate" class="form-label">Simple Rate</label>
    <input type="number" id="simple_rate" class="form-control" name="simple_rate" step="any" value="{{ $product->simple_rate ?? '' }}">
</div><br>
    </div>
    <div class="form-check"> 
        <input class="form-check-input" type="checkbox" id="spot" name="spot" value="1" value="1" {{ $product->spot ? 'checked' : '' }}>
        <label class="form-check-label" for="spot">Spot</label>
    </div>
     <div id="spotRateContainer" style="display: none;">
        <div class="form-check">
            <label for="spot_rate" class="form-label">Spot Rate</label>
            <input type="number" id="spot_rate" class="form-control" name="spot_rate" step="any" value="{{ $product->spot_rate ?? '' }}">
        </div>
    </div>
</div>

                            <!-- Corrugation -->
                            <div class="form-check">
                                <input type="hidden" name="corrugation" value="0">
                                <input class="form-check-input" type="checkbox" id="corrugation" name="corrugation" value="1" {{ $product->corrugation ? 'checked' : '' }}>
                                <label class="form-check-label" for="corrugation">Corrugation</label>
                            </div>

                            <!-- Corrugation Fields -->
                            <div id="corrugationFields" style="{{ $product->corrugation ? 'display:block;' : 'display:none;' }}">
                                <div class="mb-3"><br>
                                    <label for="csize" class="form-label">Size</label>
                                    <input type="number" id="csize" class="form-control" name="csize" value="{{ $product->curr_size }}" step="any">
                                </div>
                                
                                <div class="mb-3">
    <label for="citem" class="form-label">Item Type</label>
    <select name="citem" class="form-control select2" data-toggle="select2" id="itemTitle">
        <option value="">Select</option>
        @foreach ($itemsCo as $item)
            <option value="{{ $item->id }}" 
                data-rate="{{ $item->purchase }}" 
                data-grammage="{{ $item->grammage }}"
                {{ old('citem', $product->curr_item) == $item->id ? 'selected' : '' }}>
                {{ $item->item_code }}
            </option>
        @endforeach
    </select>
</div>


<div class="mb-3"><br>
                                    <label for="clabour" class="form-label">Box Per Labour</label>
                                    <input type="number" id="clabour" class="form-control" name="clabour" step="any" value="{{ $product->clabour }}">
                                </div>
</div>
                            <!-- Color -->
                            <div class="form-check">
                                <input type="hidden" name="noColor" value="0">
                                <input class="form-check-input" type="checkbox" id="noColor" name="noColor" value="1" {{ $product->color ? 'checked' : '' }}>
                                <label class="form-check-label" for="noColor">Color</label>
                            </div>

                            <!-- Color Fields -->
                            <div id="noColorFields" style="{{ $product->color ? 'display:block;' : 'display:none;' }}">
                                <div class="mb-3"><br>
                                    <label for="color" class="form-label">No of Color</label>
                                    <input type="number" id="color" class="form-control" name="color" value="{{ $product->color_no }}">
                                </div>
                            </div>
                            
                            
                            
                            <div class="form-check">
                                <input type="hidden" name="breaking" value="0"> <!-- Hidden input for unchecked value -->
                                <input class="form-check-input" type="checkbox" id="breaking" name="breaking" value="1" {{ $product->breaking ? 'checked' : '' }}>
                                <label class="form-check-label" for="breaking">Breaking</label>
                            </div>

                            <!-- breaking Fields -->
                            <div id="breakingFields" style="{{ $product->breaking_rate ? 'display:block;' : 'display:none;' }}">
                                <div class="mb-3"><br>
                                    <label for="breaking_rate" class="form-label">Rate Per Box</label>
                                    <input type="number" id="breaking_rate" class="form-control" name="breaking_rate" value="{{ $product->breaking_rate }}">
                                </div>
                            </div>
                            
                            
                            
                            
                            
                            
                        </div>



                        
<div class="mb-3">
                            <label for="auto_pasting_rate" class="form-label">Per Box Auto Pasting Rate</label>
                            <input type="number" id="auto_pasting_rate" class="form-control" name="auto_pasting_rate" step="any" value="{{ $product->auto_pasting_rate }}">
                        </div>
                        
                        
                         <div class="mb-3">
                            <label for="manual_pasting_rate" class="form-label">Per Box Manual Pasting Rate</label>
                            <input type="number" id="manual_pasting_rate" class="form-control" name="manual_pasting_rate" step="any" value="{{ $product->manual_pasting_rate }}">
                        </div>




                        <!-- Rate Field -->
                        <div class="mb-3">
                            <label for="rate" class="form-label">Product Rate</label>
                            <input type="number" id="rate" class="form-control" name="rate" value="{{ $product->rate }}" step="any">
                        </div>
                        
                        <div class="mb-3">
                            <label for="ups" class="form-label">No of Ups</label>
                            <input type="number" id="ups" class="form-control" name="ups" value="{{ $product->ups }}" step="any">
                        </div>

                        <!-- Description Field -->
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea id="description" class="form-control" name="description">{{ $product->descr }}</textarea>
                        </div>

                      <div class="mb-3">
    <label for="uploadFile" class="form-label">Upload File</label>
    <input type="file" id="uploadFile" class="form-control" name="file" accept="image/*">

    <!-- Live Preview for Selected File -->
    <div id="filePreviewContainer" class="mt-2" style="display: none;">
        <img id="imagePreview" src="" alt="Image Preview" style="max-width: 150px; max-height: 150px;">
        <span id="fileNamePreview" style="font-size: 14px;"></span>
        <button type="button" id="removeFile" class="btn btn-sm btn-danger mt-1">Remove</button>
    </div>

    <!-- Display the uploaded image if it exists in the database -->
    <div id="storedImageContainer" class="mt-2">
    @if (!empty($product->file_path))
        <img src="{{ asset('/storage/' . $product->file_path) }}" alt="Uploaded Image" style="width: 200px;">
        <button type="button" id="removeStoredImage" class="btn btn-sm btn-danger mt-1" data-product-id="{{ $product->id }}">Remove Stored Image</button>
    @else
        <p>No image available</p>
    @endif
</div>
</div>

                        <div class="col-12">
                            <button class="btn btn-primary" type="submit">Update</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).on('click', '#removeStoredImage', function() {
    var productId = $(this).data('product-id'); // Get the product ID from the data attribute
    var button = $(this); // Store the button element for later reference

    // Send AJAX request to remove the image
    $.ajax({
        url: '/registration_form/remove-image/' + productId,
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}', // Ensure CSRF token is included
        },
        success: function(response) {
            // On success, remove the image and button from the page
            button.closest('#storedImageContainer').html('<p>No image available</p>');
            alert(response.success); // Display success message
        },
        error: function(response) {
            alert('Error: ' + response.responseJSON.error); // Display error message
        }
    });
});

document.addEventListener("DOMContentLoaded", () => {
    const uploadFile = document.getElementById("uploadFile");
    const filePreviewContainer = document.getElementById("filePreviewContainer");
    const imagePreview = document.getElementById("imagePreview");
    const fileNamePreview = document.getElementById("fileNamePreview");
    const removeFile = document.getElementById("removeFile");

    const storedImageContainer = document.getElementById("storedImageContainer");
    const removeStoredImage = document.getElementById("removeStoredImage");

    // Show live preview when a file is selected
    uploadFile.addEventListener("change", (e) => {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = (event) => {
                imagePreview.src = event.target.result;
                imagePreview.style.display = "block";
                filePreviewContainer.style.display = "block";
                fileNamePreview.textContent = file.name;
            };
            reader.readAsDataURL(file);
        }
    });

    // Remove selected file and reset input
    removeFile.addEventListener("click", () => {
        uploadFile.value = "";
        imagePreview.src = "";
        imagePreview.style.display = "none";
        filePreviewContainer.style.display = "none";
        fileNamePreview.textContent = "";
    });

    // Optional: Remove stored image functionality (if required)
    if (removeStoredImage) {
        removeStoredImage.addEventListener("click", () => {
            // Example: Send AJAX request to remove the image
            if (confirm("Are you sure you want to remove the stored image?")) {
                // Send request to backend to delete the image
                $.ajax({
                    url: '/registration_form/remove-image/' + storedImageContainer.dataset.productId, // Get product ID from data attribute
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}', // Include CSRF token
                    },
                    success: function(response) {
                        if (response.success) {
                            storedImageContainer.innerHTML = "<p>No image available</p>";
                        } else {
                            alert("Failed to remove the image.");
                        }
                    },
                    error: function(error) {
                        console.error('Error:', error);
                        alert('Error: Unable to remove the image.');
                    }
                });
            }
        });
    }

    const laminationCheckbox = document.getElementById('lamination');
    const laminationFields = document.getElementById('laminationFields');

    // Add event listener to the checkbox
    laminationCheckbox.addEventListener('change', function() {
        if (laminationCheckbox.checked) {
            laminationFields.style.display = 'block'; // Show the fields
        } else {
            laminationFields.style.display = 'none'; // Hide the fields
        }
    });

const breakingCheckbox = document.getElementById('breaking');
    const breakingRateFields = document.getElementById('breakingFields');

    // Add event listener to the checkbox
    breakingCheckbox.addEventListener('change', function() {
        if (breakingCheckbox.checked) {
            breakingRateFields.style.display = 'block'; // Show the fields
        } else {
            breakingRateFields.style.display = 'none'; // Hide the fields
        }
    });
    
    
    const noColorCheckbox = document.getElementById('noColor');
    const noColorFields = document.getElementById('noColorFields');

    // Add event listener to the checkbox
    noColorCheckbox.addEventListener('change', function() {
        if (noColorCheckbox.checked) {
            noColorFields.style.display = 'block'; // Show the fields
        } else {
            noColorFields.style.display = 'none'; // Hide the fields
        }
    });

    const corrugationCheckbox = document.getElementById('corrugation');
    const corrugationFields = document.getElementById('corrugationFields');

    // Add event listener to the checkbox
    corrugationCheckbox.addEventListener('change', function() {
        if (corrugationCheckbox.checked) {
            corrugationFields.style.display = 'block'; // Show the fields
        } else {
            corrugationFields.style.display = 'none'; // Hide the fields
        }
    });

    document.getElementById('itemTitle').addEventListener('change', function() {
        var selectedOption = this.options[this.selectedIndex];
        var rate = selectedOption.getAttribute('data-rate');
        var grammage = selectedOption.getAttribute('data-grammage');

        document.getElementById('rate').value = rate;
        document.getElementById('grammage').value = grammage;
    });
});

// Function to handle UV checkbox changes
function handleUvChange() {
    let uvOptions = document.getElementById("uvOptions");
    uvOptions.style.display = this.checked ? "block" : "none";
}

// Function to handle Simple checkbox changes
function handleSimpleChange() {
    let simpleRateContainer = document.getElementById("simpleRateContainer");
    simpleRateContainer.style.display = this.checked ? "block" : "none";
}

// Function to handle Spot checkbox changes
function handleSpotChange() {
    let spotRateContainer = document.getElementById("spotRateContainer");
    spotRateContainer.style.display = this.checked ? "block" : "none";
}

// Add event listeners when DOM is loaded
document.addEventListener("DOMContentLoaded", function() {
    // Get the checkboxes
    const uvCheckbox = document.getElementById("uv");
    const simpleCheckbox = document.getElementById("simple");
    const spotCheckbox = document.getElementById("spot");
    
    // Add event listeners
    uvCheckbox.addEventListener("change", handleUvChange);
    simpleCheckbox.addEventListener("change", handleSimpleChange);
    spotCheckbox.addEventListener("change", handleSpotChange);
    
    // Trigger the handlers for initial state
    handleUvChange.call(uvCheckbox);
    handleSimpleChange.call(simpleCheckbox);
    handleSpotChange.call(spotCheckbox);
});
</script>

@endsection
