function search() {
    const query = document.getElementById('search').value;
    fetch(`buscar_vestuario.php?query=${query}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('results').style.display = 'block';
            const personDetails = document.getElementById('person-details');
            personDetails.innerHTML = '';
            data.forEach(person => {
                personDetails.innerHTML += `
                    <div class="person">
                        <img src="${person.foto}" alt="Foto">
                        <p><strong>Nombre:</strong> ${person.nombre}</p>
                        <p><strong>RUT:</strong> ${person.runt}</p>
                        <p><strong>Fecha de Nacimiento:</strong> ${person.fecha_nacimiento}</p>
                        <button onclick="showPdf('${person.pdf}')">Mostrar PDF de Uniformes</button>
                    </div>
                `;
            });
        });
}

function showForm() {
    document.getElementById('form-container').style.display = 'block';
}

function showPdf(pdfUrl) {
    window.open(pdfUrl, '_blank');
}