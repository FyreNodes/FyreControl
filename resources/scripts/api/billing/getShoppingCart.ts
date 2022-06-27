import http from '@/api/http';

export default async (): Promise<any> => {
    const { data } = await http.get('/api/client/billing/shoppingcart');
    return (data.data || []);
};
