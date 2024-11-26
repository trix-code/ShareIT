// Přepnutí viditelnosti formuláře pro úpravu jména
const editNameBtn = document.getElementById('editNameBtn');
const editNameForm = document.getElementById('editNameForm');
const userSettingsContainer = document.querySelector('.user-settings-container');
const updateNameForm = document.getElementById('updateNameForm');
const cancelEditName = document.getElementById('cancelEditName');

editNameBtn.addEventListener('click', () => {
    userSettingsContainer.style.display = 'none';
    editNameForm.style.display = 'block'; 
});

// Kliknutí na tlačítko "Zrušit"
cancelEditName.addEventListener('click', () => {
    userSettingsContainer.style.display = 'block';
    editNameForm.style.display = 'none'; 
});

// Odeslání formuláře pro změnu jména
updateNameForm.addEventListener('submit', (e) => {
    e.preventDefault();

    const currentName = document.getElementById('currentName').value;
    const newName = document.getElementById('newName').value;

    // AJAX požadavek na změnu jména
    fetch('php/update_name.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            currentName: currentName,
            newName: newName
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Jméno bylo úspěšně změněno.');
            window.location.reload();
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        console.error('Chyba:', error);
        alert('Chyba při aktualizaci jména.');
    });
});

function previewProfilePic(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function (e) {
            document.getElementById('profilePic').src = e.target.result;
        };
        reader.readAsDataURL(file);
        document.getElementById('uploadButton').style.display = 'block';
    }
}


// ------- NAHRAVANI FOTEK + ALERT -----------------------

function uploadProfilePic() {
    const form = document.getElementById('uploadForm');
    const formData = new FormData(form);
    
    fetch('upload_profile_pic.php', {
        method: 'POST',
        body: formData,
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const profilePicElement = document.getElementById('profilePic');
            profilePicElement.src = `img/profiles/${data.fileName}?t=${new Date().getTime()}`; // Přidání timestampu pro zabránění cachování
            alert('Profilová fotka byla úspěšně nahrána!');
        } else {
            alert('Chyba při nahrávání: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Chyba:', error);
    });
}

function previewImage(event) {
    const fileInput = event.target;
    const file = fileInput.files[0];
    const preview = document.getElementById("profilePreview");

    if (file) {
        const reader = new FileReader();
        reader.onload = () => {
            preview.src = reader.result;
        };
        reader.readAsDataURL(file);
    }
}

document.getElementById("uploadForm").addEventListener("submit", function (event) {
    const fileInput = document.getElementById("profileInput");

    if (!fileInput.files || fileInput.files.length === 0) {
        event.preventDefault(); // Zruší odeslání formuláře
        alert("Prosím, vyberte soubor před odesláním.");
    }
});


// Funkce pro zobrazení vlastního alertu
function showAlert(message, type) {
    const alert = document.getElementById("customAlert");
    const alertMessage = document.getElementById("alertMessage");

    alertMessage.textContent = message;

    // Nastylujeme alert podle typu (chyba nebo úspěch)
    if (type === "error") {
        alert.classList.add("error");
        alert.classList.remove("success");
    } else if (type === "success") {
        alert.classList.add("success");
        alert.classList.remove("error");
    }

    alert.classList.remove("hidden");
}

// Funkce pro zavření alertu
function closeAlert() {
    const alert = document.getElementById("customAlert");
    alert.classList.add("hidden");
}

// Validace při pokusu o nahrání souboru
function validateFileInput() {
    const fileInput = document.getElementById("profileInput");
    if (!fileInput.value) {
        showAlert("Nebyl vybrán žádný soubor.", "error");
        return false;
    }
    return true;
}

// Připojení k formuláři nahrávání
document.addEventListener("DOMContentLoaded", () => {
    const uploadForm = document.getElementById("uploadForm");

    if (uploadForm) {
        uploadForm.addEventListener("submit", (event) => {
            if (!validateFileInput()) {
                event.preventDefault(); // Zabráníme odeslání formuláře
            }
        });
    }

    // Zpracování URL parametrů pro zobrazení zpráv
    const urlParams = new URLSearchParams(window.location.search);
    const errorMessage = urlParams.get('error');
    const successMessage = urlParams.get('success');

    if (errorMessage) {
        showAlert(errorMessage, "error");
    } else if (successMessage) {
        showAlert(successMessage, "success");
    }
});


