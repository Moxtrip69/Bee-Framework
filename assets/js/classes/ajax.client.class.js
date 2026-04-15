export default class AjaxClient {

  async request(url, options = {}, defaultError = "Ocurrió un error en la petición.", isBlob = false) {

    let response

    try {

      response = await fetch(url, options)

    } catch (error) {

      throw new Error("No se pudo conectar con el servidor. Verifique su conexión.")

    }

    let data = null

    try {

      data = !isBlob ? await response.json() : await response.blob();

    } catch {
      // si no hay json no pasa nada
    }

    if (!response.ok) {

      const message = data?.msg || defaultError

      const err  = new Error(message)
      err.status = response.status
      err.data   = data

      throw err

    }

    return data

  }

}