import http from '@/api/http';
import { AxiosResponse } from 'axios';

export default (id: number, egg: number, name: string, description: string): Promise<any> => {
    return new Promise((resolve, reject) => {
        http.post('/api/client/billing/subscriptions/new', { id: id, egg: egg, name: name, description: description }).then((data: AxiosResponse) => resolve(data.data)).catch(reject);
    });
};
