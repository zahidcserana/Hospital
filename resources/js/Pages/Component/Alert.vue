<template>
    <div class="row">
        <div class="col-sm-4 col-3">
            <h4 class="page-title">
                {{ title ? title : 'AFC Hospital' }}
            </h4>
            <div v-if="flash.message" :type="flash.message | success"></div>
            <div v-if="Object.keys(errors).length" :type="errors | error"></div>
        </div>
        <div v-if="link" class="col-sm-8 col-9 text-right m-b-20" v-show="pending != 1 &&  approve != 1">
            <jet-responsive-nav-link
                :href="route(link)"
                :active="route().current(link)"
                class="btn btn-primary btn-rounded float-right"
            >
                <i class="fa fa-arrow-left new-btn" aria-hidden="true"></i>
                <span class="new-btn"> &nbsp; {{ label }} </span>
            </jet-responsive-nav-link>
        </div>
    </div>
</template>

<script>
import JetResponsiveNavLink from '@/Jetstream/ResponsiveNavLink'
import swal from 'sweetalert2'
window.Swal = swal

export default {
    components: {
        JetResponsiveNavLink
    },
    props: ['errors', 'title', 'flash', 'link', 'label', 'pending', 'approve'],
    filters: {
        success (status) {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: toast => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            })

            Toast.fire({
                icon: 'success',
                title: status
            })
        },
        error (errors) {
            let list = []
            $.each(errors, function (key, value) {
                list.push(value)
            })

            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: list[0],
                showConfirmButton: false,
                timer: 1500
            })
        }
    }
}
</script>
