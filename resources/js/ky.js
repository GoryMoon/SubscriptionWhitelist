import ky from 'ky'

const token = $('meta[name="csrf-token"]').attr('content');
export default ky.extend({
    hooks: {
        beforeRequest: [
            request => {
                request.headers.set('X-Requested-With', 'XMLHttpRequest');
                request.headers.set('X-CSRF-TOKEN', token);
            }
        ]
    }
});
