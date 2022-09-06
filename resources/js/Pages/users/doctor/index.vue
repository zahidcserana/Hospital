<template>
    <app-layout>
        <Models :data="data">
            <template #header>
                <Header
                    :title="title"
                    :flash="flash"
                    :errors="errors"
                    :link="link"
                    :label="label"
                />
            </template>
        </Models>
    </app-layout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout'
import Models from './components/Models'
import Header from '@/Pages/Component/Header'

export default {
    components: {
        AppLayout,
        Models,
        Header
    },
    props: ['data', 'errors', 'flash'],
    data () {
        return {
            title: 'Doctor List',
            link: 'doctors.create',
            label: 'Create Doctor'
        }
    }
}
</script>
