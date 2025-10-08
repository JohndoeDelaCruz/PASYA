<!-- Import Progress Modal -->
<div id="importProgressModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-2xl max-w-2xl w-full p-6">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-2xl font-bold text-gray-900">Import Progress</h3>
            <button onclick="closeProgressModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Progress Bar -->
        <div class="mb-6">
            <div class="flex justify-between items-center mb-2">
                <span class="text-sm font-medium text-gray-700">Processing...</span>
                <span id="progressPercentage" class="text-sm font-bold text-blue-600">0%</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-4 overflow-hidden">
                <div id="progressBar" class="bg-gradient-to-r from-blue-500 to-blue-600 h-4 rounded-full transition-all duration-300 ease-out" style="width: 0%">
                    <div class="h-full w-full bg-white opacity-20 animate-pulse"></div>
                </div>
            </div>
        </div>

        <!-- Status Message -->
        <div class="bg-gray-50 rounded-lg p-4 mb-4">
            <p id="progressMessage" class="text-sm text-gray-700">Initializing import...</p>
        </div>

        <!-- Stats Grid -->
        <div id="progressStats" class="grid grid-cols-3 gap-4 mb-4 hidden">
            <div class="bg-green-50 rounded-lg p-3 text-center">
                <div class="text-2xl font-bold text-green-600" id="successCount">0</div>
                <div class="text-xs text-green-700">Successful</div>
            </div>
            <div class="bg-red-50 rounded-lg p-3 text-center">
                <div class="text-2xl font-bold text-red-600" id="errorCount">0</div>
                <div class="text-xs text-red-700">Errors</div>
            </div>
            <div class="bg-blue-50 rounded-lg p-3 text-center">
                <div class="text-2xl font-bold text-blue-600" id="totalCount">0</div>
                <div class="text-xs text-blue-700">Total Rows</div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-end space-x-3">
            <button id="cancelButton" onclick="closeProgressModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                Close
            </button>
            <button id="viewDataButton" onclick="viewImportedData()" class="hidden px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">
                View Data
            </button>
        </div>
    </div>
</div>

<script>
let progressPollInterval = null;
let currentJobId = null;

function showProgressModal() {
    document.getElementById('importProgressModal').classList.remove('hidden');
}

function closeProgressModal() {
    if (progressPollInterval) {
        clearInterval(progressPollInterval);
        progressPollInterval = null;
    }
    document.getElementById('importProgressModal').classList.add('hidden');
}

function startImportProgress(jobId) {
    currentJobId = jobId;
    showProgressModal();
    
    // Reset UI
    updateProgress(0, 'Starting import...', false);
    document.getElementById('viewDataButton').classList.add('hidden');
    document.getElementById('progressStats').classList.add('hidden');
    
    // Start polling
    pollImportProgress();
    progressPollInterval = setInterval(pollImportProgress, 2000); // Poll every 2 seconds
}

function pollImportProgress() {
    if (!currentJobId) return;
    
    fetch(`/admin/crops/import-progress?job_id=${currentJobId}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.progress) {
            const percentage = parseFloat(data.progress.percentage) || 0;
            const message = data.progress.message || 'Processing...';
            const isComplete = percentage >= 100 || percentage < 0;
            
            updateProgress(percentage, message, isComplete);
            
            // Extract stats from message if available
            extractStatsFromMessage(message);
            
            if (isComplete) {
                clearInterval(progressPollInterval);
                progressPollInterval = null;
                
                // Show completion state
                if (percentage >= 100) {
                    document.getElementById('viewDataButton').classList.remove('hidden');
                    document.getElementById('cancelButton').textContent = 'Close';
                } else {
                    // Error state
                    document.getElementById('progressBar').classList.remove('bg-gradient-to-r', 'from-blue-500', 'to-blue-600');
                    document.getElementById('progressBar').classList.add('bg-red-500');
                }
            }
        }
    })
    .catch(error => {
        console.error('Error polling progress:', error);
        updateProgress(-1, 'Error checking progress. Please refresh the page.', true);
        clearInterval(progressPollInterval);
        progressPollInterval = null;
    });
}

function updateProgress(percentage, message, isComplete) {
    const progressBar = document.getElementById('progressBar');
    const progressPercentage = document.getElementById('progressPercentage');
    const progressMessage = document.getElementById('progressMessage');
    
    const displayPercentage = Math.max(0, Math.min(100, percentage));
    
    progressBar.style.width = displayPercentage + '%';
    progressPercentage.textContent = Math.round(displayPercentage) + '%';
    progressMessage.textContent = message;
    
    // Add completion styling
    if (isComplete && percentage >= 100) {
        progressBar.classList.remove('bg-gradient-to-r', 'from-blue-500', 'to-blue-600');
        progressBar.classList.add('bg-green-500');
        progressPercentage.classList.remove('text-blue-600');
        progressPercentage.classList.add('text-green-600');
    }
}

function extractStatsFromMessage(message) {
    // Extract success/error counts from message
    const successMatch = message.match(/Success:\s*(\d+)/i);
    const errorMatch = message.match(/Errors?:\s*(\d+)/i);
    const totalMatch = message.match(/(\d+)\/(\d+)\s+rows/i);
    
    if (successMatch || errorMatch || totalMatch) {
        document.getElementById('progressStats').classList.remove('hidden');
        
        if (successMatch) {
            document.getElementById('successCount').textContent = successMatch[1];
        }
        if (errorMatch) {
            document.getElementById('errorCount').textContent = errorMatch[1];
        }
        if (totalMatch) {
            document.getElementById('totalCount').textContent = totalMatch[2];
        }
    }
}

function viewImportedData() {
    window.location.href = '/admin/crops';
}

// Enhance existing file upload to use progress tracking
function handleFileUpload(event) {
    event.preventDefault();
    
    const form = event.target;
    const formData = new FormData(form);
    const fileInput = form.querySelector('input[type="file"]');
    const file = fileInput.files[0];
    
    if (!file) {
        alert('Please select a file to import');
        return;
    }
    
    // Show loading state
    const submitButton = form.querySelector('button[type="submit"]');
    const originalText = submitButton.textContent;
    submitButton.disabled = true;
    submitButton.textContent = 'Uploading...';
    
    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        submitButton.disabled = false;
        submitButton.textContent = originalText;
        
        if (data.is_queued && data.job_id) {
            // Large file - start progress tracking
            startImportProgress(data.job_id);
        } else if (data.success) {
            // Small file - show immediate result
            alert(data.message || 'Import completed successfully!');
            if (data.stats) {
                console.log('Import stats:', data.stats);
            }
            window.location.reload();
        } else {
            alert('Import failed: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Upload error:', error);
        submitButton.disabled = false;
        submitButton.textContent = originalText;
        alert('Upload failed. Please try again.');
    });
}

// Optional: Auto-attach to forms with class 'import-form'
document.addEventListener('DOMContentLoaded', function() {
    const importForms = document.querySelectorAll('.import-form');
    importForms.forEach(form => {
        form.addEventListener('submit', handleFileUpload);
    });
});
</script>

<style>
@keyframes shimmer {
    0% { transform: translateX(-100%); }
    100% { transform: translateX(100%); }
}

#progressBar {
    position: relative;
    overflow: hidden;
}

#progressBar::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
    animation: shimmer 2s infinite;
}
</style>
