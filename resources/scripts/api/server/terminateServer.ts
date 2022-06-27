import http from '@/api/http';
import { AxiosResponse } from 'axios';

export default (uuid: string) => {
    return new Promise((resolve, reject) => {
        http.post(`/api/client/billing/subscriptions/terminate/${uuid}`, { uuid: uuid }).then((data: AxiosResponse) => resolve(data.data || [])).catch(reject);
    });
};
