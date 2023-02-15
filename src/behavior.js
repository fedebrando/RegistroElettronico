function changeDisplay($studenti)
{
    document.getElementsByName($studenti ? "studenti" : "docenti").forEach((el) => {el.style.display = "inline";})
    document.getElementsByName(!$studenti ? "studenti" : "docenti").forEach((el) => {el.style.display = "none";})
}

function studenti()
{
    changeDisplay(true);
}

function docenti()
{
    changeDisplay(false);
}

function conferma()
{
    return confirm("Sei sicuro di voler procedere?");
}

function onloadHandler()
{
    studenti();
    document.getElementsByName("classe")[0].checked = true;
}

function coloraVoti()
{
    document.getElementsByName("votoCol").forEach((el) => {el.style.backgroundColor = (parseInt(el.innerHTML) >= 6 ? "green" : "red");});
}

function checkUsername()
{
    document.getElementById("username").value;
    return true;
}