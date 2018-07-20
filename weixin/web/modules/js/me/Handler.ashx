<%@ WebHandler Language="C#" Class="Handler" %>

using System;
using System.Collections.Generic;
using System.Drawing;
using System.Drawing.Imaging;
using System.IO;
using System.Linq;
using System.Threading;
using System.Web;

public class Handler : IHttpHandler
{

    public void ProcessRequest(HttpContext context)
    {
        var files = context.Request.Files;
        var path = "/ImgUploadWx/Files/" + DateTime.Now.ToString("yyyyMM");
        var folderPath = context.Server.MapPath(path) ;
        var relativePaths = "";
        var filename = "";
        
        if (Directory.Exists(folderPath) == false)
        {
            Directory.CreateDirectory(folderPath);
        }
        for (int i = 0; i < files.Count; i++)
        {
            var f = files[i];
            filename = DateTime.Now.ToString("yyyyMMddHHmmss") + "-" + f.FileName;
            var filePath = Path.Combine(folderPath, DateTime.Now.ToString("yyyyMMddHHmmss") + "-" + f.FileName);
            f.SaveAs(filePath);
            relativePaths += ";" + path +"/"+ filename;
        }
        if (relativePaths.Length > 0)
        {
            relativePaths = relativePaths.Substring(1, relativePaths.Length-1);
        }
        var rs = new JsonResult() { code = "0000", message = "ok",paths=relativePaths};
        context.Response.ContentType = "text/plain";
        context.Response.Write(Newtonsoft.Json.JsonConvert.SerializeObject(rs));
    }

    public bool IsReusable
    {
        get
        {
            return false;
        }
    }

}

public class JsonResult {
    public string code { get; set; }
    public string message { get; set; }
    public string paths { get; set; }
}