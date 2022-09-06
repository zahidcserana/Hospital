<template>
    <app-layout>
        <Models :data="data" :errors="errors">
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
            title: 'Patient List',
            link: 'patients.create',
            label: 'New Patient Registration'
        }
    }
}
</script>
