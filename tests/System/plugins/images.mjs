import { deflateSync } from 'node:zlib';

const crcTable = Array.from({ length: 256 }, (_, n) => {
  let c = n;

  for (let k = 0; k < 8; k += 1) {
    c = c & 1 ? 0xedb88320 ^ (c >>> 1) : c >>> 1;
  }

  return c >>> 0;
});

const crc32 = (buffer) => {
  let crc = 0xffffffff;

  for (const byte of buffer) {
    crc = crcTable[(crc ^ byte) & 0xff] ^ (crc >>> 8);
  }

  return (crc ^ 0xffffffff) >>> 0;
};

const chunk = (type, data) => {
  const length = Buffer.alloc(4);
  const crc = Buffer.alloc(4);
  const body = Buffer.concat([Buffer.from(type, 'ascii'), data]);

  length.writeUInt32BE(data.length);
  crc.writeUInt32BE(crc32(body));

  return Buffer.concat([length, body, crc]);
};

/**
 * A PNG of size x size pixels with one bit for a pixel, all 0: small on disk (the rows compress to almost nothing), and very big
 * when a server decodes it. As base64, for a test of the uploads.
 *
 * @param {number} size The width and the height in pixels.
 *
 * @returns {string}
 */
export function pixelBomb(size) {
  const header = Buffer.alloc(13);

  header.writeUInt32BE(size, 0);
  header.writeUInt32BE(size, 4);
  header[8] = 1;

  const raw = Buffer.alloc((1 + Math.ceil(size / 8)) * size);

  return Buffer.concat([
    Buffer.from([0x89, 0x50, 0x4e, 0x47, 0x0d, 0x0a, 0x1a, 0x0a]),
    chunk('IHDR', header),
    chunk('IDAT', deflateSync(raw, { level: 9 })),
    chunk('IEND', Buffer.alloc(0)),
  ]).toString('base64');
}
