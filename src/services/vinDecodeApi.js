import axios from 'axios'

// External VIN decode API service
// 专门负责与外部 VIN 解码服务通信（目前通过 Vite 代理到 Tanshu API）

// 通过 Vite 代理的本地域名路径，实际会被转发到 https://api.tanshuapi.com
// 在 vite.config.js 中已配置：
// proxy: { '/api/vin': { target: 'https://api.tanshuapi.com', ... } }
const VIN_API_ENDPOINT = '/api/vin/v2/index'

// Tanshu API 密钥（目前项目中已经在 AddProductModal.vue 中使用这一枚）
// 如需更换，请在这里统一修改。
const VIN_API_KEY = 'c5d10bc2b3e40f8a17998f8a5b7ce156'

export const vinDecodeApi = {
  /**
   * Decode VIN via external Tanshu API (through Vite proxy)
   * 通过外部 Tanshu API 解码 VIN（经由 Vite 代理）
   *
   * @param {string} vin - 17-character VIN
   * @returns {Promise<any>} - Raw JSON response from external API
   */
  async decodeVin(vin) {
    const cleanVin = String(vin || '').trim().replace(/\s+/g, '')

    if (cleanVin.length !== 17) {
      throw new Error('VIN must be exactly 17 characters')
    }

    try {
      const response = await axios.get(VIN_API_ENDPOINT, {
        params: {
          key: VIN_API_KEY,
          vin: cleanVin,
        },
        headers: {
          Accept: 'application/json',
        },
        timeout: 30000,
        // 只接受 2xx 状态码，其它当作错误抛出，由上层统一处理
        validateStatus(status) {
          return status >= 200 && status < 300
        },
      })

      return response.data
    } catch (error) {
      // 这里不做业务判断，只负责把网络/HTTP 错误抛出去
      // 上层（例如 AddProductModal.vue 的 decodeVIN）再根据需要决定如何展示给用户
      console.error('[vinDecodeApi] VIN decode request failed:', error)
      throw error
    }
  },
}

export default vinDecodeApi

