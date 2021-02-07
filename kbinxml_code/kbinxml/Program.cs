using System;
using System.IO;
using System.Security.Cryptography;
using System.Text;
using server_netcore;

namespace kbinxml
{
    class Program
    {
        static void Main(string[] args)
        {
            /*
             输入值
             <inputfile> <outputfile> <arc4> <lz77> <en-1/de-0>
             */

            //固定值
            byte[] Key =
            "00000000000069D74627D985EE2187161570D08D93B12455035B6DF0D8205DF5".HexToBytes();

            //如果参数为空直接返回
            if (args.Length == 0)
            {
                Console.WriteLine("0");
                return;
            }

            if (args[0] == null) {
                Console.WriteLine("1");
                return;
            }
            if(args[1] == null)
            {
                Console.WriteLine("2");
                return;
            }
            if (args[2] == null)
            {
                Console.WriteLine("3");
                return;
            }
            if (args[3] == null)
            {
                Console.WriteLine("4");
                return;
            }
            if (args[4] == null)
            {
                Console.WriteLine("5");
                return;
            }

            //读入文件
            byte[] data = File.ReadAllBytes(args[0]);
            Encoding.RegisterProvider(CodePagesEncodingProvider.Instance);

            if (args[4] == "1") {
                //加密

                //编码
                byte[] output = null;
                Utils.XMLToBinary(data, ref output);

                //需不需要lz77压缩,日你妈,刚删了又要加回去,草.
                if (args[3] == "lz77")
                {
                    output = Utils.CompressEmpty(output);
                }

                //需不需要加密
                if (args[2] != "none")
                {
                    //加密
                    string[] orig = args[2].Split('-');

                    byte[] part = (orig[1] + orig[2]).HexToBytes();
                    for (int i = 0; i < 6; i++)
                        Key[i] = part[i];

                    output = Utils.RC4.Encrypt(new MD5CryptoServiceProvider().ComputeHash(Key), output);

                }

                File.WriteAllBytes(args[1], output);

                //返回

                Console.WriteLine("ok");
                return;
            }
            else
            {
                //解密

                //需不需要解密
                if (args[2] != "none")
                {
                    int index = 0;

                    string[] str_array2 = args[2].Split('-');

                    do
                    {
                        Key[index] = Convert.ToByte((str_array2[1] + str_array2[2]).Substring(index << 1, 2), 0x10);
                        index++;
                    } while (index < 6);
                    byte[] rc4_key = new MD5CryptoServiceProvider().ComputeHash(Key);
                    data = Utils.RC4.Decrypt(rc4_key, data);
                }

                //需不需要lz77解压缩
                if (args[3] == "lz77")
                {
                    data = Utils.Decompress(data);
                }

                byte[] buffer = null;
                Utils.BinaryToXML(data, ref buffer);

                File.WriteAllText(args[1], Encoding.GetEncoding("Shift_JIS").GetString(buffer));

                //返回

                Console.WriteLine("ok");
                return;
            }
        }
    }
}
