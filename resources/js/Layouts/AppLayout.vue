<template>
    <div class="main-wrapper">
        <!-- Topbar -->
        <topbar />

        <!-- Sidebar -->
        <Sidebar :croute="route().current()" />

        <!-- Page Heading -->
        <!-- <header class="bg-white shadow">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <slot name="header"></slot>
            </div>
        </header> -->

        <!-- Page Content -->
        <main>
            <div class="page-wrapper">
                <div class="content">
                    <div
                        id="my-loader"
                        style="position: fixed;top: 50%;left: 50%;width: 5rem; height: 5rem;"
                    >
                        <page-loader />
                    </div>

                    <div
                        style="display:none;"
                        id="my-content"
                        class="animate-bottom"
                    >
                        <slot></slot>
                    </div>
                </div>
            </div>
        </main>

        <!-- Modal Portal -->
        <portal-target name="modal" multiple> </portal-target>
    </div>
</template>

<script>
import JetApplicationMark from '@/Jetstream/ApplicationMark'
import JetDropdown from '@/Jetstream/Dropdown'
import JetDropdownLink from '@/Jetstream/DropdownLink'
import JetNavLink from '@/Jetstream/NavLink'
import JetResponsiveNavLink from '@/Jetstream/ResponsiveNavLink'
import Sidebar from '@/Layouts/Sidebar'
import Topbar from '@/Layouts/Topbar'
import PageLoader from '@/Jetstream/PageLoader'
import Vue from 'vue'
import 'vue-loaders/dist/vue-loaders.css'
import VueLoaders from 'vue-loaders'

export default {
    components: {
        JetApplicationMark,
        JetDropdown,
        JetDropdownLink,
        JetNavLink,
        JetResponsiveNavLink,
        Topbar,
        Sidebar,
        PageLoader,
        VueLoaders
    },
    data () {
        return {
            pageLoader: true
        }
    },
    mounted () {
        document.getElementById('my-loader').style.display = 'block'
        document.getElementById('my-content').style.display = 'none'

        const plugin = document.createElement('script')
        plugin.setAttribute('src', '/assets/js/app.js')
        plugin.async = true
        document.head.appendChild(plugin)

        setTimeout(this.showPage, 500)
    },
    methods: {
        showPage () {
            document.getElementById('my-loader').style.display = 'none'
            document.getElementById('my-content').style.display = 'block'
        }
    }
}
</script>

<style>
.new-btn {
    color: white;
}
.select2-container {
    box-sizing: border-box;
    display: inline-block;
    margin: 0;
    position: relative;
    vertical-align: middle;
    width: 245px !important;
}
</style>
