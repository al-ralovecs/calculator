import config from '@/config/config.json';

function getEndpoint() {
  return config.apiUrl;
}

function request(method, resource, body = null, returnFullResponse = false, contentType = true) {
  const token = localStorage.getItem('token');

  let headers = { Authorization: token };

  if (contentType) {
    headers = { ...headers, 'Content-Type': 'application/json' };
  }

  if (returnFullResponse) {
    return fetch(getEndpoint() + resource, {
      method,
      mode: 'cors',
      credentials: 'include',
      body,
      headers,
    });
  }

  return fetch(getEndpoint() + resource, {
    method,
    mode: 'cors',
    credentials: 'include',
    body,
    headers,
  })
    // eslint-disable-next-line
    .then((response) => {
      return response.json();
    });
}

function GET(resource, returnFullResponse = false) {
  return request('GET', resource, null, returnFullResponse);
}

export function POST(resource, body, returnFullResponse = false) {
  return request('POST', resource, JSON.stringify(body), returnFullResponse);
}

export function PUT(resource, body) {
  return request('PUT', resource, JSON.stringify(body));
}

export function PATCH(resource, body) {
  return request('PATCH', resource, JSON.stringify(body));
}

export function DELETE(resource) {
  return request('DELETE', resource);
}

export function UPLOAD(resource, body) {
  return request('POST', resource, body, true, false);
}

export const api = {
  GET,
  POST,
  PUT,
  PATCH,
  DELETE,
  UPLOAD,
};
