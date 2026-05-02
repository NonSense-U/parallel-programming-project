import http from 'k6/http';
import { check, sleep } from 'k6';

const BASE_URL = 'http://127.0.0.1:8000/api';
const PRODUCT_ID = 1;
const QUANTITY = 1;

const USERS = [
    { email: 'testUser1@gmail.com', password: 'password' },
    { email: 'testUser2@gmail.com', password: 'password' },
    { email: 'testUser3@gmail.com', password: 'password' },
];

export const options = {
    scenarios: {
        checkout_race: {
            executor: 'constant-arrival-rate',
            rate: 10,
            timeUnit: '1s',
            duration: '10s',
            preAllocatedVUs: 20,
            maxVUs: 50,
        },
    },
};

function login(user) {
    const res = http.post(
        `${BASE_URL}/login`,
        JSON.stringify({
            email: user.email,
            password: user.password,
        }),
        {
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
        }
    );

    check(res, {
        'login success': (r) => r.status === 200,
    });

    return JSON.parse(res.body).data.access_token;
}


export function setup() {
    const tokens = USERS.map((u) => login(u));

    return { tokens };
}

export default function (data) {
    const token = data.tokens[__VU % data.tokens.length];

    const headers = {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json',
    };

    //! STEP 1: Add item to cart
    const addRes = http.post(
        `${BASE_URL}/cart/items`,
        JSON.stringify({
            product_id: PRODUCT_ID,
            quantity: QUANTITY,
        }),
        { headers }
    );

    check(addRes, {
        'add item ok': (r) => r.status === 200 || r.status === 201,
    });

    //! STEP 2: Checkout immediately (critical race point)
    const checkoutRes = http.post(
        `${BASE_URL}/cart/checkout`,
        JSON.stringify({}),
        { headers }
    );

    check(checkoutRes, {
        'checkout handled': (r) =>
            r.status === 200 ||
            r.status === 201 ||
            r.status === 400 || 
            r.status === 409,
    });
}