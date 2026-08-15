/**
 * SIWES Smart Allocation Engine Handler
 */

document.addEventListener('DOMContentLoaded', () => {
    const calcBtns = document.querySelectorAll('.btn-run-match');

    calcBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const studentDept = this.getAttribute('data-dept');
            const studentIndustry = this.getAttribute('data-industry');
            const studentLoc = this.getAttribute('data-loc');
            const appId = this.getAttribute('data-app-id');
            const studentId = this.getAttribute('data-student-id');
            const studentName = this.getAttribute('data-student-name');

            // Populate Modal Headers
            document.getElementById('modalStudentName').innerText = studentName;
            document.getElementById('modalStudentDept').innerText = studentDept;
            document.getElementById('modalStudentIndustry').innerText = studentIndustry;
            document.getElementById('modalStudentLoc').innerText = studentLoc;
            document.getElementById('modalAppIdInput').value = appId;
            document.getElementById('modalStudentIdInput').value = studentId;

            // Compute compatibility scores for all company options in modal
            const companyItems = document.querySelectorAll('.company-match-item');
            companyItems.forEach(item => {
                const compInd = item.getAttribute('data-comp-industry');
                const compState = item.getAttribute('data-comp-state');
                const compSlots = parseInt(item.getAttribute('data-comp-slots'));

                const match = calculateFrontendScore(studentDept, studentIndustry, studentLoc, compInd, compState, compSlots);
                
                // Update match score pill & breakdown
                const pill = item.querySelector('.match-score-pill');
                if (pill) {
                    pill.innerText = match.score + '% Match (' + match.badge + ')';
                    pill.className = 'match-score-pill badge ' + (match.score >= 80 ? 'bg-success' : (match.score >= 65 ? 'bg-warning text-dark' : 'bg-secondary'));
                }
                const progress = item.querySelector('.match-progress-bar');
                if (progress) {
                    progress.style.width = match.score + '%';
                    progress.className = 'progress-bar ' + (match.score >= 80 ? 'bg-success' : 'bg-warning');
                }
            });

            // Open Modal
            const matchModal = new bootstrap.Modal(document.getElementById('smartMatchModal'));
            matchModal.show();
        });
    });

    function calculateFrontendScore(dept, indPref, locPref, compInd, compState, slots) {
        let deptScore = 30; // standard department match base
        let indScore = (indPref.toLowerCase() === compInd.toLowerCase() || compInd.toLowerCase().includes(indPref.toLowerCase())) ? 30 : 15;
        let locScore = (locPref.toLowerCase() === compState.toLowerCase()) ? 20 : 5;
        let slotScore = slots > 10 ? 20 : (slots > 0 ? 15 : 0);

        let total = deptScore + indScore + locScore + slotScore;
        let badge = total >= 80 ? 'Optimal Match' : (total >= 60 ? 'Suitable Match' : 'Low Match');
        return { score: total, badge: badge };
    }
});
