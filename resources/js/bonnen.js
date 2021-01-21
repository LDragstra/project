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
