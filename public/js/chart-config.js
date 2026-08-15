/**
 * Chart.js Enterprise Dashboard Visualizations
 */

document.addEventListener('DOMContentLoaded', () => {
    // 1. Students by Department Doughnut Chart
    const deptChartCtx = document.getElementById('departmentChart');
    if (deptChartCtx) {
        new Chart(deptChartCtx, {
            type: 'doughnut',
            data: {
                labels: ['Computer Science', 'Electrical Eng', 'Information Tech', 'Statistics'],
                datasets: [{
                    data: [42, 28, 18, 12],
                    backgroundColor: ['#16A34A', '#166534', '#10B981', '#F59E0B'],
                    borderWidth: 2,
                    borderColor: '#FFFFFF'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
    }

    // 2. Allocation Status Bar Chart
    const allocChartCtx = document.getElementById('allocationChart');
    if (allocChartCtx) {
        new Chart(allocChartCtx, {
            type: 'bar',
            data: {
                labels: ['Allocated', 'Approved (Pending)', 'Under Review', 'Rejected'],
                datasets: [{
                    label: 'Number of Students',
                    data: [65, 24, 15, 6],
                    backgroundColor: ['#16A34A', '#10B981', '#F59E0B', '#DC2626'],
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    }
});
