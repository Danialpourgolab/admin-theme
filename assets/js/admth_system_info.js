document.addEventListener('DOMContentLoaded', function () {
    const expandBtn = document.getElementById('admthSystemExpandBtn');
    const infoPanel = document.getElementById('admthSystemInfoPanel');

    let panelExpanded = false;

    expandBtn.addEventListener('click', function () {
        if (panelExpanded) {
            infoPanel.style.width = '0';
        } else {
            infoPanel.style.width = '300px'; // Adjust the width as needed
        }

        panelExpanded = !panelExpanded;
    });
});
