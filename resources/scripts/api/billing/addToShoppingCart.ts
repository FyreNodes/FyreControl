import http from '@/api/http';

export default async (pid: number, amount = 1): Promise<any> => {
    const { data } = await http.post('/api/client/billing/shoppingcart', {
        pid: pid,
        amount: amount,
    });
    return (data.data || []);
};
