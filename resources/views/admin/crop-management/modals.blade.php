<!-- Add Crop Type Modal -->
<div id="addCropModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Add New Crop Type</h3>
                <button onclick="closeAddCropModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <form id="addCropForm" onsubmit="submitAddCrop(event)">
                <div class="mb-4">
                    <label for="crop_type_name" class="block text-sm font-medium text-gray-700 mb-2">Crop Type Name</label>
                    <input type="text" id="crop_type_name" name="crop_type_name" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                           placeholder="Enter crop type name">
                    <p class="mt-1 text-sm text-gray-500">Enter a unique crop type name</p>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeAddCropModal()" 
                            class="px-4 py-2 text-gray-600 border border-gray-300 rounded-md hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                        <span id="addCropSubmitText">Add Crop Type</span>
                        <svg id="addCropSpinner" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Crop Type Modal -->
<div id="editCropModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Edit Crop Type</h3>
                <button onclick="closeEditCropModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <form id="editCropForm" onsubmit="submitEditCrop(event)">
                <input type="hidden" id="edit_crop_old_name" name="old_name">
                <div class="mb-4">
                    <label for="edit_crop_type_name" class="block text-sm font-medium text-gray-700 mb-2">Crop Type Name</label>
                    <input type="text" id="edit_crop_type_name" name="crop_type_name" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="Enter crop type name">
                    <p class="mt-1 text-sm text-gray-500">Update the crop type name</p>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeEditCropModal()" 
                            class="px-4 py-2 text-gray-600 border border-gray-300 rounded-md hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <span id="editCropSubmitText">Update Crop Type</span>
                        <svg id="editCropSpinner" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Municipality Modal -->
<div id="addMunicipalityModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Add New Municipality</h3>
                <button onclick="closeAddMunicipalityModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <form id="addMunicipalityForm" onsubmit="submitAddMunicipality(event)">
                <div class="mb-4">
                    <label for="municipality_name" class="block text-sm font-medium text-gray-700 mb-2">Municipality Name</label>
                    <input type="text" id="municipality_name" name="municipality_name" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="Enter municipality name">
                    <p class="mt-1 text-sm text-gray-500">Enter a unique municipality name</p>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeAddMunicipalityModal()" 
                            class="px-4 py-2 text-gray-600 border border-gray-300 rounded-md hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <span id="addMunicipalitySubmitText">Add Municipality</span>
                        <svg id="addMunicipalitySpinner" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Municipality Modal -->
<div id="editMunicipalityModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Edit Municipality</h3>
                <button onclick="closeEditMunicipalityModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <form id="editMunicipalityForm" onsubmit="submitEditMunicipality(event)">
                <input type="hidden" id="edit_municipality_old_name" name="old_name">
                <div class="mb-4">
                    <label for="edit_municipality_name" class="block text-sm font-medium text-gray-700 mb-2">Municipality Name</label>
                    <input type="text" id="edit_municipality_name" name="municipality_name" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="Enter municipality name">
                    <p class="mt-1 text-sm text-gray-500">Update the municipality name</p>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeEditMunicipalityModal()" 
                            class="px-4 py-2 text-gray-600 border border-gray-300 rounded-md hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <span id="editMunicipalitySubmitText">Update Municipality</span>
                        <svg id="editMunicipalitySpinner" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteConfirmModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Confirm Deletion</h3>
                <button onclick="closeDeleteConfirmModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="mb-4">
                <div class="flex items-center mb-3">
                    <svg class="w-12 h-12 text-red-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.268 15.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                    <div>
                        <p class="text-sm text-gray-600">Are you sure you want to delete</p>
                        <p class="font-medium text-gray-900" id="deleteItemName"></p>
                        <p class="text-xs text-red-600 mt-1">This action cannot be undone.</p>
                    </div>
                </div>
            </div>
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closeDeleteConfirmModal()" 
                        class="px-4 py-2 text-gray-600 border border-gray-300 rounded-md hover:bg-gray-50">
                    Cancel
                </button>
                <button type="button" onclick="confirmDelete()" 
                        class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                    <span id="deleteSubmitText">Delete</span>
                    <svg id="deleteSpinner" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    // Global variables for delete operations
    let deleteType = '';
    let deleteItem = '';

    // Combined Modal Functions
    function openAddCombinedModal() {
        document.getElementById('addCombinedModal').classList.remove('hidden');
        document.getElementById('crop_type_name').focus();
    }

    function closeAddCombinedModal() {
        document.getElementById('addCombinedModal').classList.add('hidden');
        document.getElementById('addCombinedForm').reset();
    }

    // Crop Type Modal Functions
    function openAddCropModal() {
        document.getElementById('addCropModal').classList.remove('hidden');
        document.getElementById('crop_type_name').focus();
    }

    function closeAddCropModal() {
        document.getElementById('addCropModal').classList.add('hidden');
        document.getElementById('addCropForm').reset();
    }

    function openEditCropModal() {
        document.getElementById('editCropModal').classList.remove('hidden');
        document.getElementById('edit_crop_type_name').focus();
    }

    function closeEditCropModal() {
        document.getElementById('editCropModal').classList.add('hidden');
        document.getElementById('editCropForm').reset();
    }

    // Municipality Modal Functions
    function openAddMunicipalityModal() {
        document.getElementById('addMunicipalityModal').classList.remove('hidden');
        document.getElementById('municipality_name').focus();
    }

    function closeAddMunicipalityModal() {
        document.getElementById('addMunicipalityModal').classList.add('hidden');
        document.getElementById('addMunicipalityForm').reset();
    }

    function openEditMunicipalityModal() {
        document.getElementById('editMunicipalityModal').classList.remove('hidden');
        document.getElementById('edit_municipality_name').focus();
    }

    function closeEditMunicipalityModal() {
        document.getElementById('editMunicipalityModal').classList.add('hidden');
        document.getElementById('editMunicipalityForm').reset();
    }

    // Delete Confirmation Modal
    function openDeleteConfirmModal(type, item) {
        deleteType = type;
        deleteItem = item;
        document.getElementById('deleteItemName').textContent = `"${item}"?`;
        document.getElementById('deleteConfirmModal').classList.remove('hidden');
    }

    function closeDeleteConfirmModal() {
        document.getElementById('deleteConfirmModal').classList.add('hidden');
        deleteType = '';
        deleteItem = '';
    }

    // Edit Functions
    function editCropType(cropType) {
        document.getElementById('edit_crop_old_name').value = cropType;
        document.getElementById('edit_crop_type_name').value = cropType;
        openEditCropModal();
    }

    function editMunicipality(municipality) {
        document.getElementById('edit_municipality_old_name').value = municipality;
        document.getElementById('edit_municipality_name').value = municipality;
        openEditMunicipalityModal();
    }

    // Delete Functions
    function deleteCropType(cropType) {
        openDeleteConfirmModal('crop', cropType);
    }

    function deleteMunicipality(municipality) {
        openDeleteConfirmModal('municipality', municipality);
    }

    // Form Submission Functions
    async function submitAddCombined(event) {
        event.preventDefault();
        const form = event.target;
        const formData = new FormData(form);
        
        setLoadingState('addCombined', true);
        
        try {
            const response = await fetch('/admin/crop-management/combined', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: formData
            });
            
            const data = await response.json();
            
            if (data.success) {
                showMessage('Crop type and municipality added successfully!');
                closeAddCombinedModal();
                setTimeout(() => location.reload(), 1000);
            } else {
                showMessage(data.message || 'An error occurred', 'error');
            }
        } catch (error) {
            showMessage('Network error occurred', 'error');
        } finally {
            setLoadingState('addCombined', false);
        }
    }

    async function submitAddCrop(event) {
        event.preventDefault();
        const form = event.target;
        const formData = new FormData(form);
        
        setLoadingState('addCrop', true);
        
        try {
            const response = await fetch('/admin/crop-management/crop-types', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: formData
            });
            
            const data = await response.json();
            
            if (data.success) {
                showMessage('Crop type added successfully!');
                closeAddCropModal();
                setTimeout(() => location.reload(), 1000);
            } else {
                showMessage(data.message || 'An error occurred', 'error');
            }
        } catch (error) {
            showMessage('Network error occurred', 'error');
        } finally {
            setLoadingState('addCrop', false);
        }
    }

    async function submitEditCrop(event) {
        event.preventDefault();
        const form = event.target;
        const formData = new FormData(form);
        const oldName = formData.get('old_name');
        
        setLoadingState('editCrop', true);
        
        try {
            const response = await fetch(`/admin/crop-management/crop-types/${encodeURIComponent(oldName)}`, {
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    crop_type_name: formData.get('crop_type_name')
                })
            });
            
            const data = await response.json();
            
            if (data.success) {
                showMessage('Crop type updated successfully!');
                closeEditCropModal();
                setTimeout(() => location.reload(), 1000);
            } else {
                showMessage(data.message || 'An error occurred', 'error');
            }
        } catch (error) {
            showMessage('Network error occurred', 'error');
        } finally {
            setLoadingState('editCrop', false);
        }
    }

    async function submitAddMunicipality(event) {
        event.preventDefault();
        const form = event.target;
        const formData = new FormData(form);
        
        setLoadingState('addMunicipality', true);
        
        try {
            const response = await fetch('/admin/crop-management/municipalities', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: formData
            });
            
            const data = await response.json();
            
            if (data.success) {
                showMessage('Municipality added successfully!');
                closeAddMunicipalityModal();
                setTimeout(() => location.reload(), 1000);
            } else {
                showMessage(data.message || 'An error occurred', 'error');
            }
        } catch (error) {
            showMessage('Network error occurred', 'error');
        } finally {
            setLoadingState('addMunicipality', false);
        }
    }

    async function submitEditMunicipality(event) {
        event.preventDefault();
        const form = event.target;
        const formData = new FormData(form);
        const oldName = formData.get('old_name');
        
        setLoadingState('editMunicipality', true);
        
        try {
            const response = await fetch(`/admin/crop-management/municipalities/${encodeURIComponent(oldName)}`, {
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    municipality_name: formData.get('municipality_name')
                })
            });
            
            const data = await response.json();
            
            if (data.success) {
                showMessage('Municipality updated successfully!');
                closeEditMunicipalityModal();
                setTimeout(() => location.reload(), 1000);
            } else {
                showMessage(data.message || 'An error occurred', 'error');
            }
        } catch (error) {
            showMessage('Network error occurred', 'error');
        } finally {
            setLoadingState('editMunicipality', false);
        }
    }

    async function confirmDelete() {
        setLoadingState('delete', true);
        
        const url = deleteType === 'crop' 
            ? `/admin/crop-management/crop-types/${encodeURIComponent(deleteItem)}`
            : `/admin/crop-management/municipalities/${encodeURIComponent(deleteItem)}`;
            
        try {
            const response = await fetch(url, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            });
            
            const data = await response.json();
            
            if (data.success) {
                showMessage(`${deleteType === 'crop' ? 'Crop type' : 'Municipality'} deleted successfully!`);
                closeDeleteConfirmModal();
                setTimeout(() => location.reload(), 1000);
            } else {
                showMessage(data.message || 'An error occurred', 'error');
            }
        } catch (error) {
            showMessage('Network error occurred', 'error');
        } finally {
            setLoadingState('delete', false);
        }
    }

    // Loading state management
    function setLoadingState(type, loading) {
        const spinner = document.getElementById(`${type}Spinner`);
        const text = document.getElementById(`${type}SubmitText`);
        
        if (loading) {
            spinner.classList.remove('hidden');
            text.style.display = 'none';
        } else {
            spinner.classList.add('hidden');
            text.style.display = 'inline';
        }
    }

    // Close modals when clicking outside
    window.onclick = function(event) {
        const modals = ['addCombinedModal', 'addCropModal', 'editCropModal', 'addMunicipalityModal', 'editMunicipalityModal', 'deleteConfirmModal'];
        modals.forEach(modalId => {
            const modal = document.getElementById(modalId);
            if (event.target === modal) {
                modal.classList.add('hidden');
            }
        });
    }

    // Close modals with Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            const modals = ['addCombinedModal', 'addCropModal', 'editCropModal', 'addMunicipalityModal', 'editMunicipalityModal', 'deleteConfirmModal'];
            modals.forEach(modalId => {
                document.getElementById(modalId).classList.add('hidden');
            });
        }
    });
</script>