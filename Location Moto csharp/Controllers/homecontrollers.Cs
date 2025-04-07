using Microsoft.AspNetCore.Mvc;

namespace LocationMoto.Controllers
{
    [ApiController]
    [Route("/")]
    public class HomeController : ControllerBase
    {
        [HttpGet]
        public IActionResult Index()
        {
            return Ok("Bienvenue sur l’API LocationMoto 🚀");
        }
    }
}
