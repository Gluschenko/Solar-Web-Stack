function GetLocation(url){
    var a = document.createElement("a");
    a.href = url; // => http://example.com:3000/pathname/?search=test#hash

    return {
        protocol: a.protocol, // => "http:"
        host: a.host,         // => "example.com:3000"
        hostname: a.hostname, // => "example.com"
        port: a.port,         // => "3000"
        pathname: a.pathname, // => "/pathname/"
        hash: a.hash,         // => "#hash"
        search: a.search,     // => "?search=test"
        origin: a.origin,     // => "http://example.com:3000"
        uri: a.pathname + a.search + a.hash,
    };
};

function Navigate(url) {
    document.location = url;
}

function Reload() {
    document.location.reload(true);
}

function ToJSON(arr) {
    return JSON.stringify(arr);
}

function FromJSON(str) {
    return JSON.parse(str);
}

function Write(id, val) {
    Find(id).innerHTML = val;
}

function WriteForward(id, val) {
    Find(id).innerHTML = val + Find(id).innerHTML;
}

function WriteEnd(id, val) {
    Find(id).innerHTML += val;
}

function Clear(id) {
    Write(id, "");
}

function Delete(id) {
    Find(id).parentNode.removeChild(Find(id));
}

function Find(id) {
    var obj = document.getElementById(id);
    return obj;
}

function Hide(id, param) {
    if (param == null) param = "none";

    if (document.getElementById(id)) {
        document.getElementById(id).style.display = param;
    }
}

function Show(id, param) {
    if (param == null) param = "block";

    if (document.getElementById(id)) {
        document.getElementById(id).style.display = param;
    }
}

function Hidden(id) {
    if (Find(id).style.display == "none") return true;
    return false;
}

function SetVisibility(id, state, visible_param)
{
    if (!visible_param) visible_param = "block";

    if (state) {
        Show(id, visible_param);
    }
    else {
        Hide(id);
    }
}

function Exists(id) {
    if (document.getElementById(id)) return true;
    return false;
}


