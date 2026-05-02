import http from 'k6/http';
import { check, sleep } from 'k6';

export const options = {
    scenarios: {
        ecommerce_flow: {
            executor: 'ramping-vus',
            startVUs: 10,
            stages: [
                // { duration: '30s', target: 30 },
                // { duration: '1m', target: 60 },
                // { duration: '2m', target: 100 },
                // { duration: '1m', target: 100 },
                { duration: '30s', target: 0 },
            ],
            gracefulRampDown: '30s',
        },
    },

    thresholds: {
        http_req_failed: ['rate<0.05'],
        http_req_duration: ['p(95)<2000'],
    },
};

const BASE_URL = 'http://localhost:8000/api';

export default function () {
    const token = login();

    getProducts();

    getProductDetails();

    // addToCart(token);

    // viewCart(token);

    // checkout(token);

    // getOrders(token);

    sleep(1);
}

function login() {
    const payload = JSON.stringify({
        email: `testUser${__VU}@gmail.com`,
        password: 'password',
    });

    const params = {
        headers: {
            'Content-Type': 'application/json',
        },
    };

    const res = http.post(`${BASE_URL}/login`, payload, params);

    check(res, {
        'login success': (r) => r.status === 200,
    });

    return res.json('token');
}

function getProducts() {
    const res = http.get(`${BASE_URL}/products`);

    check(res, {
        'products loaded': (r) => r.status === 200,
    });
}

function getProductDetails() {
    const res = http.get(`${BASE_URL}/products/1`);

    check(res, {
        'product details loaded': (r) => r.status === 200,
    });
}

function addToCart(token) {
    const payload = JSON.stringify({
        product_id: 1,
        quantity: 1,
    });

    const params = {
        headers: {
            Authorization: `Bearer ${token}`,
            'Content-Type': 'application/json',
        },
    };

    const res = http.post(`${BASE_URL}/cart/items`, payload, params);

    check(res, {
        'add to cart success': (r) => r.status === 200,
    });
}

function viewCart(token) {
    const params = {
        headers: {
            Authorization: `Bearer ${token}`,
        },
    };

    const res = http.get(`${BASE_URL}/cart/show`, params);

    check(res, {
        'view cart success': (r) => r.status === 200,
    });
}

function checkout(token) {
    const payload = JSON.stringify({
        payment_method: 'cash',
    });

    const params = {
        headers: {
            Authorization: `Bearer ${token}`,
            'Content-Type': 'application/json',
        },
    };

    const res = http.post(`${BASE_URL}/cart/checkout`, payload, params);

    check(res, {
        'checkout success': (r) => r.status === 200,
    });
}

function getOrders(token) {
    const params = {
        headers: {
            Authorization: `Bearer ${token}`,
        },
    };

    const res = http.get(`${BASE_URL}/orders`, params);

    check(res, {
        'orders loaded': (r) => r.status === 200,
    });
}