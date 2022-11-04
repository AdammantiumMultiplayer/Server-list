// ✅ Change button text on click
function handleClick(btn) {
    //var btn = document.getElementById('btnstart');
    initialText = btn.innerHTML

    btn.innerHTML = "<i class='fa fa-spinner fa-spin'></i>Loading";
    setTimeout(() => {
        btn.innerHTML = initialText;
    }, 6000);
};