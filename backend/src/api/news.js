import request from '@/utils/request'

export function fetchRecord(id) {
  return request({
    url: `/news/${id}`,
    method: 'get'
  })
}
