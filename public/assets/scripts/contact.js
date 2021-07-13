const error = document.getElementById('error')
const success = document.getElementById('success')
console.log(success.innerHTML, error.innerHTML)
if (error.innerHTML != "") {
    error.classList = 'alert alert-danger alert-dismissable d-flex justify-content-evenly'
    success.classList = 'visually-hidden'
}
if (success.innerHTML != "") {
    success.classList = 'alert alert-success alert-dismissable d-flex justify-content-evenly'
    error.classList = 'visually-hidden'
    setTimeout(() => {
        window.location.href = '/home/accueil'
    }, 3000)
}