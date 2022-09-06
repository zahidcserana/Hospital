import axios from 'axios';

// const HTTP = axios s
export const HTTP = axios.create({
    baseURL: `http://afc.local/`,
    // baseURL: `https://afchms.cerebrum.host/`,
    headers: {
        Authorization: 'Bearer {token}'
    }
})
