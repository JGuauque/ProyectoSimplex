document.addEventListener('DOMContentLoaded', function () {
    fetch('/api/grafico-datos')
        .then(response => response.json())
        .then(data => {
            // Configuración del gráfico con datos dinámicos
            var ctx2 = document.getElementById('doughnut').getContext('2d');
            var myChart2 = new Chart(ctx2, {
                type: 'doughnut',
                data: {
                    labels: ['Clientes', 'Instructores', 'Usuarios', 'Proveedores'],
                    datasets: [{
                        label: 'Datos',
                        data: [data.clientes, data.instructores, data.usuarios, data.proveedores],
                        backgroundColor: [
                            'rgba(41, 155, 99, 1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 206, 86, 1)',
                            'rgba(120, 46, 139,1)'
                        ],
                        borderColor: [
                            'rgba(41, 155, 99, 1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 206, 86, 1)',
                            'rgba(120, 46, 139,1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true
                }
            });
        })
        .catch(error => console.error('Error al obtener los datos:', error));
});



// var ctx2 = document.getElementById('doughnut').getContext('2d');
// var myChart2 = new Chart(ctx2, {
//     type: 'doughnut',
//     data: {
//         labels: ['Clientes', 'Instructores', 'Usuarios', 'Proveedores'],

//         datasets: [{
//             label: 'Datos',
//             data: [110, 42, 71, 12],
//             backgroundColor: [
//                 'rgba(41, 155, 99, 1)',
//                 'rgba(54, 162, 235, 1)',
//                 'rgba(255, 206, 86, 1)',
//                 'rgba(120, 46, 139,1)'

//             ],
//             borderColor: [
//                 'rgba(41, 155, 99, 1)',
//                 'rgba(54, 162, 235, 1)',
//                 'rgba(255, 206, 86, 1)',
//                 'rgba(120, 46, 139,1)'

//             ],
//             borderWidth: 1
//         }]

//     },
//     options: {
//         responsive: true
//     }
// });