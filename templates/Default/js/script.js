function download(content, fileName, contentType) {
    var a = document.createElement("a");
    var file = new Blob([content], {type: contentType});
    a.href = URL.createObjectURL(file);
    a.download = fileName;
    a.click();
}

document.forms.ourForm.onsubmit = function(e){
    e.preventDefault();
    
    let dateFrom = document.forms.ourForm.dateFrom.value;
    let dateTo = document.forms.ourForm.dateTo.value;

    let xhr = new XMLHttpRequest();

    xhr.open('POST', '/');
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.responseType = 'json';

    xhr.onload = () => {
        download(JSON.stringify(xhr.response), 'dump.json', 'application/json');
    }

    xhr.send('dateFrom=' + dateFrom + "&dateTo=" + dateTo);
};