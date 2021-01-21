toastr.options.progressBar = true;
toastr.options.timeOut = 10000;
toastr.options.showMethod = "slideDown";
toastr.options.hideMethod = "slideUp";
toastr.options.closeMethod = "slideUp";
toastr.options.extendedTimeOut = 3000;
toastr.options.positionClass = "toast-bottom-right";

async function postAll(id, data, vapData) {
    await addOrder(id, data, vapData);
    await markReceipt(id);
}

async function addOrder(id, data, vapData) {
    try {
        let response = await axios.post("addOrder", { id, data, vapData });
        toastr.success(
            "Factuur is aangemaakt in Snelstart!",
            "Factuur aangemaakt"
        );
        document.querySelector("#submit-" + id).innerHTML =
            "<div id='spinner-" +
            id +
            "' class='spinner-border spinner-border-sm'></div> Factuur aan VAP toevoegen..";
    } catch (err) {
        toastr.error(err, "Factuur niet geimporteerd in SnelStart!");
        document.querySelector("#submit-" + id).innerHTML =
            "Verkoopfactuur maken van bovenstaande bon";
        document.querySelector("#submit-" + id).classList.remove("disabled");
    }
}

async function markReceipt(id) {
    try {
        let response = await axios.post("markReceipt", { id });
        toastr.success("Bon als verwerkt gemarkeerd!", "Markeren");
        document.querySelector("#submit-" + id).innerHTML = "Verwerkt";
    } catch (err) {
        toastr.error(err, "Bon niet verwerkt als gemarkeerd!");
        document.querySelector("#submit-" + id).innerHTML =
            "Verkoopfactuur maken van bovenstaande bon";
        document.querySelector("#submit-" + id).classList.remove("disabled");
    }
}

function makeInvoice(id) {
    document.querySelector("#submit-" + id).innerHTML =
        "<div id='spinner-" +
        id +
        "' class='spinner-border spinner-border-sm'></div> Factureren..";
    document.querySelector("#submit-" + id).classList.add("disabled");
    document.querySelector("#submit-" + id).setAttribute("disabled", "true");
    document.querySelector("#delete-" + id).setAttribute("disabled", "true");
    let projectId = document.querySelector("#project-" + id).value;
    let today = document.querySelector("#date-" + id).value;
    let termijn = document.querySelector("#termijn-" + id).value;

    let projectNaam = document.querySelector("#projectNaam-" + id).textContent;
    let klant = document.querySelector("#select-" + id).value;
    let artikel = document.querySelector("#artikel-" + id).value;
    let arikelSelected = document.querySelector("#artikel-" + id).selectedIndex;
    let artikelNaamOriginal = document.querySelector("#artikel-" + id).options[
        arikelSelected
    ].text;
    let artikelNaam = artikelNaamOriginal
        .replace("%projectnaam%", projectNaam)
        .split("- ")
        .pop();

    let sjabloon = document.querySelector("#sjabloon-" + id).value;

    let bedrag = document.querySelector("#bedrag-" + id).value;
    let freelance = '';
    let mergePDF = 0;

    if (document.querySelector('#fl-' + id).checked) {
        freelance = 'Ja';
    }
    if (document.querySelector('#pdf-' + id).checked) {
        mergePDF = 1;
    }

    let omschrijving = document.querySelector("#toelichting-" + id).value;
    var lines = omschrijving.split('\n');

    var regels = [{
        artikel: {
            id: artikel
        },
        stuksprijs: bedrag,
        aantal: 1,
        totaal: bedrag,
        omschrijving: artikelNaam
    }];
    for (var line = 0; line < lines.length; line++) {
        extraRegel = {
            stuksprijs: 0,
            omschrijving: lines[line]
        }
        regels.push(extraRegel);
    }

    let weekNr = document.querySelector("#week-" + id).value;
    let verkoopOmschrijving = projectNaam.slice(0, 19) + '... wk ' + weekNr;

    let vapData = {
        projectId: projectId,
        bonId: id,
        fl: freelance,
        merge: mergePDF,
        factuurbedrag: bedrag,
        datum: today,
        omschrijving: omschrijving,
        krediettermijn: termijn,
        'klant': klant
    };
    let data = {
        relatie: {
            id: klant
        },
        VerkooporderBtwIngaveModel: 2,
        procesStatus: "Order",
        datum: today,
        krediettermijn: termijn,
        omschrijving: verkoopOmschrijving,
        regels: regels,
        verkoopordersjabloon: {
            id: sjabloon
        }
    };

    if (klant && sjabloon && artikel && termijn) {
        postAll(id, data, vapData);
    } else if (!sjabloon) {
        toastr.error(
            "Geen sjabloon gekozen voor de factuur.",
            "Sjabloon niet gevonden!"
        );
        btnBehavior(id, "sjabloon");
    } else if (!klant) {
        toastr.error(
            "Klant vanuit Snelstart moet gekoppeld worden met een klant uit VAP.",
            "Klant niet gevonden!"
        );
        btnBehavior(id, "select");
    } else if (!artikel) {
        toastr.error("Geen artikel geselecteerd!.", "Artikel niet gevonden!");
        btnBehavior(id, "artikel");
    } else if (!termijn) {
        toastr.error(
            "Graag het termijn in dagen in te vullen!.",
            "Betalingstermijn niet ingevuld!"
        );
        btnBehavior(id, "termijn");
    }
}

function btnBehavior(id, type) {
    document.querySelector("#" + type + "-" + id).focus();
    document.querySelector("#submit-" + id).removeAttribute("disabled");
    document.querySelector("#submit-" + id).classList.remove("disabled");
    document.querySelector("#submit-" + id).innerHTML =
        "Verkoopfactuur maken van bovenstaande bon";
    document.querySelector("#delete-" + id).removeAttribute("disabled");
}

function deleteBon(id) {
    axios
        .post("factuur/" + id)
        .then(function (response) {
            toastr.info(
                "Bon gemarkeerd met status 'gefactureerd'",
                "Bon verwerkt"
            );
            document.getElementById("row-" + id).remove();
            document.getElementById("hr-" + id).remove();
        })
        .catch(function (error) {
            console.log(error);
            toastr.error(error, "Bon niet verwerkt!");
        });
}

function setTexts(nummer, id, email) {
    document.querySelector('#invoiceModalTitle').textContent = 'Factuur ' + nummer;
    document.querySelector('#hiddenModal').value = id;
    if (email) {
        document.querySelector('#invoiceEmail').value = email;
    } else {
        document.querySelector('#invoiceEmail').value = '';
    }
}

function sendForm() {
    let id = document.querySelector('#hiddenModal').value;
    let count = parseInt(document.querySelector('#telling-' + id).textContent);

    let email = document.querySelector('#invoiceEmail').value;
    let text = document.querySelector('#text').value;

    if (!email)
        return toastr.error(
            "Mailadres niet ingevuld",
            "Factuur is niet verzonden"
        );
    axios.post("factuurVersturen", {
        factuurId: id,
        email: email,
        text: text
    })
        .then(function (response) {
            document.querySelector('#telling-' + id).textContent = count + 1;
            toastr.success(response.data.msg, 'Verzonden');
        })
        .catch(function (error) {
            toastr.error(email + ' is geen juist emailadres', 'Niet verzonden');
        });

}

async function deleteSalesOrder(id) {

    try {
        let response = await axios.get("factuur/delete/" + id);
        document.querySelector('#delete-' + id).disabled = true;
        toastr.success(response.data.msg, 'Verwijderd');
        console.log(response);
    } catch (err) {
        toastr.error(err, 'Niet verwijderd');
    }
}
function newData() {
    document.querySelector("#dataRefresh").innerHTML =
        '<div class="spinner-border spinner-border-sm text-primary"></div>';

    axios
        .get("bonnen/cache")
        .then(function(response) {
            toastr.success(response.data, "Gelukt!");

            toastr.info(
                "Alle gegevens van de diverse BV's worden vernieuwd. Het laden van de nieuwe data neemt even wat tijd in beslag.",
                "Data"
            );
            document.querySelector("#dataRefresh").innerHTML =
                '<i class="fa fa-refresh text-primary" aria-hidden="true" style="cursor:pointer;" title="Data refreshen"></i>';
            setTimeout(function() {
                location.reload(true);
            }, 5000);
        })
        .catch(function(error) {
            toastr.error(error, "Er is iets niet goed gegaan!");
        });
}

async function initialPayment(id, amount, internNummer) {
    try {
        document.getElementById(id).innerHTML =
            "<div id='spinner-" +
            id +
            "' class='spinner-border spinner-border-sm'></div> Moment..";
        let response = await axios.post("initial-payment", {id, amount, internNummer});
        toastr.success(
            "Initiele betaling geregistreerd!",
            "Factuur afgevinkt"
        );
        document.getElementById(id).innerHTML = 'Klaar';
        document.getElementById(id).disabled = true;
    } catch (err) {
        toastr.error(err, "Initiele betaling niet geregistreerd!");
        document.getElementById(id).innerHTML = '€' + amount;
        document.getElementById(id).disabled = False;
    }
}

async function restPayment(id, amount) {
    try {
        document.getElementById(id).innerHTML =
            "<div id='spinner-" +
            id +
            "' class='spinner-border spinner-border-sm'></div> Moment..";
        let response = await axios.post("rest-payment", {id, amount});
        toastr.success(
            "Restant betaling geregistreerd! Factuur is nu volledig verwerkt.",
            "Factuur verwerkt"
        );
        document.getElementById(id).innerHTML = 'Klaar';
        document.getElementById(id).disabled = true;
    } catch (err) {
        toastr.error(err, "Restant betaling niet geregistreerd!");
    }
}


let data ={
    numbers:{
        amount: []
    },
    totals:{
        amount: 0
    }
}

function countTotals(){
    let totalAmount = 0;
    for(let i = 0; i < data.numbers.amount.length; i++){
        totalAmount += data.numbers.amount[i];
    }

    data.totals.amount = totalAmount;
}

function updateCounter(amount) {
    data.numbers.amount.push(amount);
    countTotals();
}

$("input:checkbox").on("change", function () {
    let amount = parseFloat($(this).closest('tr').find('.count').text().replace(',', '.').replace('€', ''));
    if ($(this).prop('checked')===false) {
        amount = -amount;
    }

    updateCounter(amount);

    document.querySelector('.amount').textContent = new Intl.NumberFormat('nl-NL', { style: 'currency', currency: 'EUR' }).format(data.totals.amount);
});

function deleteInitial(id){
    if(confirm("Factuur van de factoringlijst afhalen?")){
        try{
            axios.get("initial-payment/delete/"+id);
            toastr.success('Factuur verwijderd van lijst.');
            document.getElementById("row-"+id).closest("tr").remove();
        } catch(e) {
            toastr.error(e.response);
        }
    }
}
