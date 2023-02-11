import Swal from "sweetalert2";

export default
{
    success(message) {
        Swal.fire({
            toast: true,
            icon: 'success',
            title: message,
            customClass: 'is-success',
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            onOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
            }
        });
    },

    error(message = 'Oops! Something went wrong, please try again.') {
        Swal.fire({
            toast: true,
            icon: 'error',
            title: message,
            customClass: 'is-error',
            position: 'top-end',
            showConfirmButton: false,
            timer: 6000,
            timerProgressBar: true,
            onOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
            }
        });
    }
}
