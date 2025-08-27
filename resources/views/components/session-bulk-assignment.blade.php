@props([
    'sessions' => [],
    'instructors' => [],
    'title' => 'Bulk Instructor Assignment',
    'show' => false
])

<!-- Bulk Assignment Modal -->
<div id="bulkAssignmentModal" class="{{ $show ? '' : 'hidden' }} fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-4xl shadow-lg rounded-md bg-white">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-medium text-gray-900">{{ $title }}</h3>
            <button onclick="closeBulkAssignmentModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <form id="bulkAssignmentForm" class="space-y-6">
            @csrf
            
            <!-- Selected Sessions Info -->
            <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
                <h4 class="text-sm font-medium text-blue-900 mb-2">Sessions yang Dipilih</h4>
                <div id="selectedSessionsList" class="text-sm text-blue-800">
                    <!-- Will be populated by JavaScript -->
                </div>
                <div class="mt-2">
                    <span class="text-xs text-blue-600">Total: <span id="selectedSessionsCount">0</span> sessions</span>
                </div>
            </div>

            <!-- Assignment Type -->
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tipe Assignment</label>
                    <div class="grid grid-cols-2 gap-4">
                        <label class="relative flex items-start">
                            <div class="flex items-center h-5">
                                <input type="radio" name="assignment_type" value="instructor_only" 
                                       class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300" checked>
                            </div>
                            <div class="ml-3 text-sm">
                                <div class="font-medium text-gray-900">Instruktur Saja</div>
                                <div class="text-gray-500">Assign instruktur utama ke sessions</div>
                            </div>
                        </label>
                        
                        <label class="relative flex items-start">
                            <div class="flex items-center h-5">
                                <input type="radio" name="assignment_type" value="instructor_and_assistant" 
                                       class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300">
                            </div>
                            <div class="ml-3 text-sm">
                                <div class="font-medium text-gray-900">Instruktur + Asisten</div>
                                <div class="text-gray-500">Assign instruktur dan asisten</div>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Instructor Selection -->
                <div>
                    <label for="bulk_instructor" class="block text-sm font-medium text-gray-700 mb-2">
                        Pilih Instruktur <span class="text-red-500">*</span>
                    </label>
                    <select name="user_id_instruktur" id="bulk_instructor" required
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Pilih Instruktur</option>
                        @foreach($instructors as $instructor)
                            <option value="{{ $instructor->id }}">{{ $instructor->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Assistant Selection (Initially Hidden) -->
                <div id="assistantSelection" class="hidden">
                    <label for="bulk_assistant" class="block text-sm font-medium text-gray-700 mb-2">
                        Pilih Asisten (Opsional)
                    </label>
                    <select name="user_id_asisten" id="bulk_assistant"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Tidak Ada Asisten</option>
                        @foreach($instructors as $instructor)
                            <option value="{{ $instructor->id }}">{{ $instructor->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Assignment Options -->
            <div class="border-t border-gray-200 pt-6">
                <h4 class="text-sm font-medium text-gray-900 mb-4">Opsi Assignment</h4>
                
                <div class="space-y-3">
                    <label class="flex items-start">
                        <div class="flex items-center h-5">
                            <input type="checkbox" name="check_conflicts" value="1" checked
                                   class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded">
                        </div>
                        <div class="ml-3 text-sm">
                            <div class="text-gray-900">Cek Konflik Jadwal</div>
                            <div class="text-gray-500">Sistem akan mengecek apakah instruktur memiliki konflik jadwal</div>
                        </div>
                    </label>
                    
                    <label class="flex items-start">
                        <div class="flex items-center h-5">
                            <input type="checkbox" name="skip_conflicts" value="1"
                                   class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded">
                        </div>
                        <div class="ml-3 text-sm">
                            <div class="text-gray-900">Skip Sessions dengan Konflik</div>
                            <div class="text-gray-500">Sessions yang berbenturan akan dilewat, yang lain tetap di-assign</div>
                        </div>
                    </label>
                    
                    <label class="flex items-start">
                        <div class="flex items-center h-5">
                            <input type="checkbox" name="override_existing" value="1"
                                   class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded">
                        </div>
                        <div class="ml-3 text-sm">
                            <div class="text-gray-900">Override Assignment Existing</div>
                            <div class="text-gray-500">Ganti instruktur yang sudah ada pada sessions</div>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Preview Section -->
            <div id="assignmentPreview" class="hidden bg-yellow-50 border border-yellow-200 rounded-md p-4">
                <h4 class="text-sm font-medium text-yellow-900 mb-2">Preview Assignment</h4>
                <div id="previewContent" class="text-sm text-yellow-800">
                    <!-- Will be populated by JavaScript -->
                </div>
            </div>

            <!-- Conflict Results -->
            <div id="conflictResults" class="hidden">
                <!-- Will be populated by JavaScript -->
            </div>

            <!-- Form Actions -->
            <div class="flex flex-col sm:flex-row justify-end space-y-3 sm:space-y-0 sm:space-x-3 pt-6 border-t border-gray-200">
                <button type="button" onclick="previewAssignment()" 
                        class="inline-flex justify-center items-center px-4 py-2 bg-yellow-600 text-white text-sm font-medium rounded-md hover:bg-yellow-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                    Preview
                </button>
                
                <button type="button" onclick="closeBulkAssignmentModal()" 
                        class="inline-flex justify-center items-center px-4 py-2 bg-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-400">
                    Batal
                </button>
                
                <button type="submit" 
                        class="inline-flex justify-center items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Assign Instruktur
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
// Toggle assistant selection based on assignment type
document.addEventListener('DOMContentLoaded', function() {
    const assignmentTypeRadios = document.querySelectorAll('input[name="assignment_type"]');
    const assistantSelection = document.getElementById('assistantSelection');
    
    assignmentTypeRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.value === 'instructor_and_assistant') {
                assistantSelection.classList.remove('hidden');
            } else {
                assistantSelection.classList.add('hidden');
                document.getElementById('bulk_assistant').value = '';
            }
        });
    });
});

function showBulkAssignmentModal() {
    const selectedSessions = document.querySelectorAll('.session-checkbox:checked');
    
    if (selectedSessions.length === 0) {
        alert('Pilih minimal satu session untuk di-assign');
        return;
    }
    
    // Update selected sessions display
    updateSelectedSessionsDisplay(selectedSessions);
    
    // Show modal
    document.getElementById('bulkAssignmentModal').classList.remove('hidden');
}

function closeBulkAssignmentModal() {
    document.getElementById('bulkAssignmentModal').classList.add('hidden');
    
    // Reset form
    document.getElementById('bulkAssignmentForm').reset();
    document.getElementById('assistantSelection').classList.add('hidden');
    document.getElementById('assignmentPreview').classList.add('hidden');
    document.getElementById('conflictResults').classList.add('hidden');
}

function updateSelectedSessionsDisplay(selectedSessions) {
    const sessionsList = document.getElementById('selectedSessionsList');
    const sessionsCount = document.getElementById('selectedSessionsCount');
    
    let html = '';
    let count = 0;
    
    selectedSessions.forEach((checkbox, index) => {
        const row = checkbox.closest('tr');
        const sessionInfo = extractSessionInfo(row);
        
        if (index < 5) { // Show only first 5
            html += `
                <div class="flex justify-between items-center py-1">
                    <span>${sessionInfo.program} - Pertemuan ${sessionInfo.meeting}</span>
                    <small class="text-blue-600">${sessionInfo.date}</small>
                </div>
            `;
        }
        count++;
    });
    
    if (count > 5) {
        html += `<div class="text-center py-1 text-blue-600">... dan ${count - 5} sessions lainnya</div>`;
    }
    
    sessionsList.innerHTML = html;
    sessionsCount.textContent = count;
}

function extractSessionInfo(row) {
    // Extract session info from table row
    // This is a simplified version - adjust based on your actual table structure
    const cells = row.querySelectorAll('td');
    
    return {
        program: cells[2]?.textContent?.trim() || 'Unknown Program',
        meeting: cells[1]?.textContent?.match(/\d+/)?.[0] || '?',
        date: cells[3]?.textContent?.trim() || 'Unknown Date'
    };
}

function previewAssignment() {
    const form = document.getElementById('bulkAssignmentForm');
    const formData = new FormData(form);
    const selectedSessions = Array.from(document.querySelectorAll('.session-checkbox:checked'))
                                  .map(cb => cb.value);
    
    if (!formData.get('user_id_instruktur')) {
        alert('Pilih instruktur terlebih dahulu');
        return;
    }
    
    // Add selected session IDs to form data
    selectedSessions.forEach(sessionId => {
        formData.append('session_ids[]', sessionId);
    });
    
    const previewDiv = document.getElementById('assignmentPreview');
    const previewContent = document.getElementById('previewContent');
    
    previewDiv.classList.remove('hidden');
    previewContent.innerHTML = '<div class="text-yellow-600">Menggenerate preview...</div>';
    
    // Mock preview - replace with actual API call
    setTimeout(() => {
        const instructorName = form.querySelector('#bulk_instructor option:checked').textContent;
        const assistantName = form.querySelector('#bulk_assistant option:checked')?.textContent || 'Tidak ada';
        
        previewContent.innerHTML = `
            <div class="space-y-2">
                <div><strong>Instruktur:</strong> ${instructorName}</div>
                <div><strong>Asisten:</strong> ${assistantName}</div>
                <div><strong>Sessions:</strong> ${selectedSessions.length} sessions akan di-assign</div>
                <div class="text-yellow-700 text-xs mt-2">
                    Klik "Assign Instruktur" untuk melanjutkan proses assignment.
                </div>
            </div>
        `;
    }, 1000);
}

// Handle form submission
document.getElementById('bulkAssignmentForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const selectedSessions = Array.from(document.querySelectorAll('.session-checkbox:checked'))
                                  .map(cb => cb.value);
    
    if (selectedSessions.length === 0) {
        alert('Tidak ada sessions yang dipilih');
        return;
    }
    
    const formData = new FormData(this);
    
    // Add selected session IDs
    selectedSessions.forEach(sessionId => {
        formData.append('session_ids[]', sessionId);
    });
    
    // Add action type
    formData.append('action', 'assign_instructor');
    
    // Submit to bulk endpoint
    fetch('{{ route("ekstrakurikuler.sessions.bulk") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message || 'Instructor berhasil di-assign');
            location.reload();
        } else {
            alert('Error: ' + (data.message || 'Gagal assign instructor'));
        }
    })
    .catch(error => {
        alert('Terjadi kesalahan: ' + error.message);
    });
});
</script>
@endpush