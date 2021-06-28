document.forms.ourForm.onsubmit = function(e){
    e.preventDefault();
    
    let dateFrom = document.forms.ourForm.dateFrom.value;
    let dateTo = document.forms.ourForm.dateTo.value;

    let xhr = new XMLHttpRequest();

    xhr.open('POST', '/');
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    let myVar = setInterval(function() {
        progressBar();
    }, 1000);

    xhr.responseType = 'json';

    xhr.onload = () => {
        clearInterval(myVar);
        console.log("ok");

        let field = document.getElementById("process");
        field.innerHTML = "Загрузка завершена !!!";
        
        let fileName = xhr.getResponseHeader('content-disposition').split('filename=')[1].split(';')[0];
        download(document.URL + '/' + fileName);
    }

    xhr.send('dateFrom=' + dateFrom + "&dateTo=" + dateTo);
};

function progressBar() {
    let xhr = new XMLHttpRequest();

    xhr.open('POST', '/');
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xhr.onload = () => {
        let field = document.getElementById("process");
        field.innerHTML = "Выполнено: " + xhr.response + "%";
    }

    xhr.send('progBar');
}

function download(url) {
	
	var link_url = document.createElement("a");
	
	link_url.download = url.substring((url.lastIndexOf("/") + 1), url.length);
	link_url.href = url;
	document.body.appendChild(link_url);
	link_url.click();
	document.body.removeChild(link_url);
	delete link_url;

}
