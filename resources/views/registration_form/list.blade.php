@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">Product Registration</h4>
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
                <form id="voucherForm" action="{{ route('registration_form.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="col-6">
                        <input type="hidden" id="invoice_type" name="v_type" value="LPN" readonly>
                        <input type="hidden" id="invoice" name="invoice_number">
                        <input type="hidden" id="totalAmount" name="total_amount" value="0">

<div class="mb-3">
                            <label for="product_type" class="form-label">Product Type</label>
                            <select name="product_type" class="form-control select2" data-toggle="select2" id="product_type">
                                <option value="">Select</option>
                                <option value="Local">Local</option>
                                <option value="Export">Export</option>
                            </select>
                        </div>

                        <!-- Supplier Selection -->
                        <div class="mb-3">
                            <label for="entryParty" class="form-label">Party</label>
                            <select name="account" class="form-control select2" id="entryParty"
                                data-toggle="select2">
                                <option value="">Select</option>
                                @foreach ($accounts->where('level2_id', 7) as $account)
                                <option value="{{ $account->id }}">{{ $account->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="productName" class="form-label">Product Name</label>
                            <input type="text" id="productName" class="form-control" name="productName">
                        </div>

                        <!-- Country Dropdown -->
                        <div class="mb-3">
                            <label for="country" class="form-label">Country</label>
                            <select name="country" class="form-control select2" id="country" data-toggle="select2">
                                <option value="">Select</option>
                                @foreach ($countries as $country)
                                <option value="{{ $country->id }}">{{ $country->country_name }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Item Type Dropdown -->
                      <div class="mb-3">
    <label for="mainItemTitle" class="form-label">Item Type</label>
    <select name="item" class="form-control select2" id="mainItemTitle" data-toggle="select2">
        <option value="">Select</option>
        @foreach ($items as $item)
        <option value="{{ $item->id }}" data-grammage="{{ $item->gramage }}">
            {{ $item->item_code }}
        </option>
        @endforeach
    </select>
</div>

                        <!-- Grammage Field -->
                        <div class="mb-3">
                            <label for="grammage" class="form-label">Grammage</label>
                            <input type="number" id="grammage" class="form-control" name="grammage" step="any">
                        </div>

                        <!-- Length Field -->
                        <div class="mb-3">
                            <label for="length" class="form-label">Length</label>
                            <input type="number" id="length" class="form-control" name="length" step="any">
                        </div>

                        <!-- Width Field -->
                        <div class="mb-3">
                            <label for="width" class="form-label">Width</label>
                            <input type="number" id="width" class="form-control" name="width" step="any">
                        </div>

                        <!-- Checkbox Section -->
                        <div class="card p-3 mt-3">
                            <h5 class="mb-3">Options</h5>

                            <!-- Lamination -->
                            <div class="form-check form-check-inline">
                                <input type="hidden" name="lamination" value="0"> <!-- Hidden input for unchecked value -->
                                <input class="form-check-input" type="checkbox" id="lamination" name="lamination" value="1">
                                <label class="form-check-label" for="lamination">Lamination</label>
                            </div>

                            <!-- Lamination Fields -->
                            <div id="laminationFields" style="display: none;">
                                <div class="mb-3"><br>
                                    <label for="lsize" class="form-label">Size</label>
                                    <input type="number" id="lsize" class="form-control" name="lsize" step="any">
                                </div>
                                <div class="mb-3">
    <label for="litemTitle" class="form-label">Item Type</label>
    <select name="litem" class="form-control select2" id="litemTitle" data-toggle="select2">
        <option value="">Select</option>
        @foreach ($items as $item)
        @if ($item->type_id == 4)
        <option value="{{ $item->id }}" data-rate="{{ $item->purchase }}" data-grammage="{{ $item->grammage }}">
            {{ $item->item_code }}
        </option>
        @endif
        @endforeach
    </select>
</div>
                                <div class="mb-3"><br>
                                    <label for="limpression" class="form-label">No. of Impression</label>
                                    <input type="number" id="limpression" class="form-control" name="limpression" step="any">
                                </div>
                            </div>

                            <!-- UV -->
                            <div class="form-check">
    <input type="hidden" name="uv" value="0"> <!-- Hidden input for unchecked value -->
    <input class="form-check-input" type="checkbox" id="uv" name="uv" value="1">
    <label class="form-check-label" for="uv">UV</label>
</div>

<div id="uvOptions" style="display: none; margin-top: 10px; margin-bottom: 10px; margin-left: 20px;">
    <div class="form-check">
        <input class="form-check-input" type="checkbox" id="simple" name="simple" value="1">
        <label class="form-check-label" for="simple">Simple</label>
    </div>
    <div id="simpleRateContainer" style="display: none;">
        <div class="form-check">
            <label for="simple_rate" class="form-label">Simple Rate</label>
            <input type="number" id="simple_rate" class="form-control" name="simple_rate" step="any">
        </div><br>
    </div>
    
    <div class="form-check">
        <input class="form-check-input" type="checkbox" id="spot" name="spot" value="1">
        <label class="form-check-label" for="spot">Spot</label>
    </div>
    
    <div id="spotRateContainer" style="display: none;">
        <div class="form-check">
            <label for="spot_rate" class="form-label">Spot Rate</label>
            <input type="number" id="spot_rate" class="form-control" name="spot_rate" step="any">
        </div>
    </div>
</div>


                            
                            
                            

                            <!-- Corrugation -->
                            <div class="form-check">
                                <input type="hidden" name="corrugation" value="0"> <!-- Hidden input for unchecked value -->
                                <input class="form-check-input" type="checkbox" id="corrugation" name="corrugation" value="1">
                                <label class="form-check-label" for="corrugation">Corrugation</label>
                            </div>

                            <!-- Corrugation Fields -->
                            <div id="corrugationFields" style="display: none;">
                                <div class="mb-3"><br>
                                    <label for="csize" class="form-label">Size</label>
                                    <input type="number" id="csize" class="form-control" name="csize" step="any">
                                </div>
                                <div class="mb-3">
    <label for="citemTitle" class="form-label">Item Type</label>
    <select name="citem" class="form-control select2" id="citemTitle" data-toggle="select2">
        <option value="">Select</option>
        @foreach ($items as $item)
        @if ($item->type_id == 2)
        <option value="{{ $item->id }}" data-rate="{{ $item->purchase }}" data-grammage="{{ $item->grammage }}">
            {{ $item->item_code }}
        </option>
        @endif
        @endforeach
    </select>
</div>
                                <div class="mb-3"><br>
                                    <label for="clabour" class="form-label">Per Box Labour</label>
                                    <input type="number" id="clabour" class="form-control" name="clabour" step="any">
                                </div>
                            </div>

                            <!-- Color -->
                            <div class="form-check">
                                <input type="hidden" name="noColor" value="0"> <!-- Hidden input for unchecked value -->
                                <input class="form-check-input" type="checkbox" id="noColor" name="noColor" value="1">
                                <label class="form-check-label" for="noColor">Color</label>
                            </div>

                            <!-- Color Fields -->
                            <div id="noColorFields" style="display: none;">
                                <div class="mb-3"><br>
                                    <label for="color" class="form-label">No of Color</label>
                                    <input type="number" id="color" class="form-control" name="color">
                                </div>
                            </div>
                            
                            <!-- breaking -->
                            <div class="form-check">
                                <input type="hidden" name="breaking" value="0"> <!-- Hidden input for unchecked value -->
                                <input class="form-check-input" type="checkbox" id="breaking" name="breaking" value="1">
                                <label class="form-check-label" for="breaking">Breaking</label>
                            </div>

                            <!-- breaking Fields -->
                            <div id="breakingFields" style="display: none;">
                                <div class="mb-3"><br>
                                    <label for="breaking_rate" class="form-label">Rate Per Box</label>
                                    <input type="number" id="breaking_rate" class="form-control" name="breaking_rate">
                                </div>
                            </div>
                        </div>
                        
                        
                        
<div class="mb-3">
                            <label for="auto_pasting_rate" class="form-label">Per Box Auto Pasting Rate</label>
                            <input type="number" id="auto_pasting_rate" class="form-control" name="auto_pasting_rate" step="any">
                        </div>
                        
                        
                         <div class="mb-3">
                            <label for="manual_pasting_rate" class="form-label">Per Box Manual Pasting Rate</label>
                            <input type="number" id="manual_pasting_rate" class="form-control" name="manual_pasting_rate" step="any">
                        </div>
                        
                                
                        <!-- Rate Field -->
                        <div class="mb-3">
                            <label for="rate" class="form-label">Product Rate</label>
                            <input type="number" id="rate" class="form-control" name="rate" step="any">
                        </div>
                        
                        <div class="mb-3">
                            <label for="ups" class="form-label">No of Ups</label>
                            <input type="number" id="ups" class="form-control" name="ups" step="any">
                        </div>
                        
                        <!-- Description Field -->
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea id="description" class="form-control" name="description"></textarea>
                        </div>
                        <div class="mb-3">
    <label for="uploadFile" class="form-label">Upload File</label>
    <input type="file" id="uploadFile" class="form-control" name="file" accept="image/*">
    <div id="filePreviewContainer" class="mt-2" style="display:none;">
        <img id="imagePreview" src="" alt="Image Preview" style="max-width: 150px; max-height: 150px; display:none;">
        <span id="fileNamePreview" style="font-size:14px;"></span>
        <button type="button" id="removeFile" class="btn btn-sm btn-danger">X</button>
    </div>
</div>
                        <button type="submit" class="btn btn-success">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</div>


<script>
    // Get file input and preview elements
    const uploadFile = document.getElementById('uploadFile');
    const filePreviewContainer = document.getElementById('filePreviewContainer');
    const imagePreview = document.getElementById('imagePreview');
    const fileNamePreview = document.getElementById('fileNamePreview');
    const removeFileButton = document.getElementById('removeFile');

    // Event listener for file input
    uploadFile.addEventListener('change', function(event) {
        const file = event.target.files[0];

        // Check if a file was selected
        if (file) {
            const reader = new FileReader();

            // Set up the image preview when the file is loaded
            reader.onload = function(e) {
                imagePreview.src = e.target.result;  // Set image source to the file content
                filePreviewContainer.style.display = 'block';  // Show the preview container
                imagePreview.style.display = 'block';  // Show the image preview
                fileNamePreview.textContent = file.name;  // Show the file name
            };

            // Read the file as a data URL to display the image preview
            reader.readAsDataURL(file);
        } else {
            filePreviewContainer.style.display = 'none';  // Hide the preview container if no file is selected
        }
    });

    // Event listener for remove file button
    removeFileButton.addEventListener('click', function() {
        uploadFile.value = '';  // Clear the file input
        filePreviewContainer.style.display = 'none';  // Hide the preview container
        imagePreview.style.display = 'none';  // Hide the image preview
        fileNamePreview.textContent = '';  // Clear the file name
    });
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
    document.getElementById('mainItemTitle').addEventListener('change', function() {
        var selectedOption = this.options[this.selectedIndex];
        var rate = selectedOption.getAttribute('data-rate');
        var grammage = selectedOption.getAttribute('data-grammage');

        document.getElementById('rate').value = rate;
        document.getElementById('grammage').value = grammage;
    });
    
   document.getElementById("uv").addEventListener("change", function() {
    let uvOptions = document.getElementById("uvOptions");
    uvOptions.style.display = this.checked ? "block" : "none";
});

document.getElementById("simple").addEventListener("change", function() {
    let simpleRateContainer = document.getElementById("simpleRateContainer");
    simpleRateContainer.style.display = this.checked ? "block" : "none";
});

document.getElementById("spot").addEventListener("change", function() {
    let spotRateContainer = document.getElementById("spotRateContainer");
    spotRateContainer.style.display = this.checked ? "block" : "none";
});
</script>
@endsection