<template>
    <app-layout>
        <ReferralStatement :param="param">
            <template #header>
                <Alert
                    :title="title"
                    :flash="flash"
                    :errors="errors"
                    :link="link"
                    :label="label"
                />
            </template>
        </ReferralStatement>
    </app-layout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout'
import ReferralStatement from './components/referralStatement'
import Alert from '@/Pages/Component/Alert'

export default {
    components: {
        AppLayout,
        ReferralStatement,
        Alert
    },
    props: ['param', 'errors', 'flash'],
    data () {
        return {
            title: 'Referral Statement',
            link: 'referralSetting.create',
            label: 'New Referral Setting'
        }
    }
}
</script>
