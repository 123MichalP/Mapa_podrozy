// Inicjalizacja mapy
const map = L.map('map').setView([51.505, -0.09], 2);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
}).addTo(map);

var greenIcon = new L.Icon({
    iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-green.png',
    shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
    iconSize: [25, 41],
    iconAnchor: [12, 41],
    popupAnchor: [1, -34],
    shadowSize: [41, 41]
  });
  
  var blueIcon = new L.Icon({
    iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-blue.png',
    shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
    iconSize: [25, 41],
    iconAnchor: [12, 41],
    popupAnchor: [1, -34],
    shadowSize: [41, 41]
  });

  var redIcon = new L.Icon({
    iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
    shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
    iconSize: [25, 41],
    iconAnchor: [12, 41],
    popupAnchor: [1, -34],
    shadowSize: [41, 41]
  });
  
let tempMarker = null; // Zmienna przechowująca tymczasowy marker
let markers = []; // Tablica do przechowywania trwałych markerów

// Dodawanie tymczasowego markera na mapie po kliknięciu
map.on('click', function (e) {
    // Jeśli istnieje tymczasowy marker, usuń go
    if (tempMarker) {
        map.removeLayer(tempMarker);
    }

    // Utwórz nowy tymczasowy marker
    tempMarker = L.marker(e.latlng).addTo(map);

    // Przechowywanie współrzędnych tymczasowego markera
    const latitude = e.latlng.lat;
    const longitude = e.latlng.lng;

    // Ustawienie akcji dla przycisku zapisu
    document.getElementById('save-place').onclick = function () {
        savePlace(latitude, longitude);
        map.removeLayer(tempMarker); // Usuń tymczasowy marker po zapisaniu
        tempMarker = null;
    };
});
// Obsługa tworzenia nowej grupy
document.getElementById('create-group-btn').addEventListener('click', function() {
    const groupName = document.getElementById('group-name').value;
    const groupColor = document.getElementById('group-color').value;  // Pobieramy kolor grupy

    if (!groupName.trim() || !groupColor) {
        alert('Nazwa grupy i kolor są wymagane!');
        return;
    }

    const formData = new FormData();
    formData.append('group_name', groupName);
    formData.append('group_color', groupColor);  // Przesyłamy kolor grupy

    fetch('create_group.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            alert('Grupa została utworzona!');
            location.reload(); // Odświeżenie strony, aby pokazać nową grupę
        } else {
            alert(data.message || 'Błąd podczas tworzenia grupy.');
        }
    })
    // .catch(error => console.error('Błąd:', error));
});

// Funkcja zapisywania miejsca
function savePlace(latitude, longitude) {
    const name = document.getElementById('place-name').value.trim();
    const description = document.getElementById('place-description').value.trim();
    const groupId = document.getElementById('place-group').value;

    if (!name || !description) {
        alert('Proszę wypełnić wszystkie pola.');
        return;
    }

    const formData = new FormData();
    formData.append('name', name);
    formData.append('description', description);
    formData.append('latitude', latitude);
    formData.append('longitude', longitude);
    formData.append('group_id', groupId);

    fetch('save_place.php', {
        method: 'POST',
        body: formData,
    })
        .then((response) => response.json())
        .then((data) => {
            if (data.status === 'success') {
                alert('Miejsce zapisane!');
                loadPlaces(); // Przeładuj wszystkie miejsca
            } else {
                alert('Błąd zapisu!');
            }
        })
        .catch((error) => console.error('Error:', error));
}

function loadPlaces(groupIds = []) {
    // Jeśli żadna grupa nie jest wybrana, usuń wszystkie znaczniki z mapy i zakończ
    if (groupIds.length === 0) {
        // Usuń istniejące markery
        markers.forEach((marker) => map.removeLayer(marker));
        markers = [];
        return; // Nie wykonuj dalszych działań
    }

    const formData = new FormData();
    formData.append('group_ids', JSON.stringify(groupIds));

    fetch('get_places.php', {
        method: 'POST',
        body: formData,
    })
        .then((response) => response.json())
        .then((data) => {
            // Usuń istniejące markery z mapy
            markers.forEach((marker) => map.removeLayer(marker));
            markers = [];

            // Przechowuj ikony w zależności od kolorów grup
            const icons = {
                red: redIcon,
                blue: blueIcon,
                green: greenIcon,
            };

            // Dodaj nowe markery
            data.forEach((place) => {
                const position = [parseFloat(place.latitude), parseFloat(place.longitude)];
                const groupColor = place.group_color || 'red'; // Domyślny kolor

                // Wybierz odpowiednią ikonę
                const icon = icons[groupColor] || redIcon;

                const marker = L.marker(position, { icon }).addTo(map);

                marker.bindPopup(`
                    <h3>${place.name}</h3>
                    <p>${place.description}</p>
                    <button onclick="deletePlace(${place.latitude}, ${place.longitude})">Usuń</button>
                `);
                
                markers.push(marker);
            });
        })
        .catch((error) => console.error('Błąd podczas ładowania miejsc:', error));    
}


// Funkcja usuwania miejsca
function deletePlace(latitude, longitude) {
    // Wyślij żądanie usunięcia do serwera
    fetch('delete_place.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `latitude=${latitude}&longitude=${longitude}`, // Dane usunięcia
    })
    .then((response) => response.json())
    .then((data) => {
        if (data.success) {
            alert('Miejsce zostało usunięte.');

            // Usuń marker z mapy i z tablicy markerów
            markers = markers.filter((marker) => {
                if (marker.getLatLng().lat === latitude && marker.getLatLng().lng === longitude) {
                    map.removeLayer(marker); // Usuń marker z mapy
                    return false; // Usuń go także z tablicy
                }
                return true; // Zostaw pozostałe markery
            });
        } else {
            alert('Błąd podczas usuwania miejsca.');
        }
    })
    .catch((error) => console.error('Błąd podczas usuwania miejsca:', error));
}


document.getElementById('group-selection-form').addEventListener('change', () => {
    const selectedGroups = Array.from(
        document.querySelectorAll('.group-checkbox:checked')
    ).map((cb) => cb.value);
    loadPlaces(selectedGroups); // Ładowanie miejsc dla wybranych grup
});


document.addEventListener('DOMContentLoaded', () => {
    const checkboxes = document.querySelectorAll('.group-checkbox');

    checkboxes.forEach(checkbox => {
        const color = checkbox.getAttribute('data-color');
        checkbox.style.accentColor = color; // Ustawia kolor checkboxa
    });
});
