function exportToExcel() {
    var location = 'data:application/vnd.ms-excel;base64,';
    var excelTemplate = '<html> ' +
        '<head> ' +
        '<meta http-equiv="content-type" content="text/plain; charset=UTF-8"/> ' +
        '</head> ' +
        '<body> ' +
        document.getElementById("table-responsive").innerHTML +
        '</body> ' +
        '</html>'
    window.location.href = location + window.btoa(excelTemplate);
}

function export_all() {
    const url = new URL(window.location.href);
    const periode_filter = url.searchParams.get('periode_filter');
    let currentUrl = window.location.origin;
    let newUrl = `${APP_URL}/pdrb_ringkasan_export_all`;
    if (periode_filter) {
        newUrl += `?periode_filter=${periode_filter}`;
    }
    window.open(newUrl, '_blank');
}