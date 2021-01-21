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