import http from '@/api/http';

interface Plans {
    name: string;
    description: string;
    price: string;
}

export default async (): Promise<Plans[]> => {
    return new Promise((resolve, reject) => {
        http.get('/api/client/billing/products').then((data) => resolve(data.data || [])).catch(reject);
    });
};
